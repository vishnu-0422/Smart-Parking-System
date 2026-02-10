<?php
/**
 * Payment Controller
 * Handles payment processing and booking management
 */

require_once __DIR__ . '/../config/db.php';

class PaymentController {
    
    /**
     * Get booking information for payment
     */
    public function getBookingInfo($vehicleId, $slotId) {
        try {
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("
                SELECT b.*, v.vehicle_number, s.slot_number, s.price_per_hour as rate_per_hour
                FROM bookings b
                JOIN vehicles v ON b.vehicle_id = v.id
                JOIN slots s ON b.slot_id = s.id
                WHERE b.vehicle_id = ? AND b.slot_id = ? AND b.exit_time IS NULL
                ORDER BY b.created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$vehicleId, $slotId]);
            $booking = $stmt->fetch();
            
            if ($booking) {
                // Calculate duration and amount
                $entryTime = new DateTime($booking['entry_time']);
                $expiryTime = new DateTime($booking['expiry_time']);
                $duration = $entryTime->diff($expiryTime);
                $hours = $duration->h + ($duration->days * 24);
                if ($hours < 1) $hours = 1;
                
                $booking['duration'] = $hours . ' hour' . ($hours > 1 ? 's' : '');
                $booking['total_amount'] = $hours * $booking['rate_per_hour'];
                
                return [
                    'success' => true,
                    'booking' => $booking
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }
        } catch (Exception $e) {
            error_log("PaymentController::getBookingInfo - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving booking information'
            ];
        }
    }
    
    /**
     * Process payment
     */
    public function processPayment($data) {
        try {
            $pdo = getDBConnection();
            
            // Get booking
            $stmt = $pdo->prepare("
                SELECT b.*, s.price_per_hour
                FROM bookings b
                JOIN slots s ON b.slot_id = s.id
                WHERE b.vehicle_id = ? AND b.slot_id = ? AND b.exit_time IS NULL
                ORDER BY b.created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$data['vehicleId'], $data['slotId']]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }
            
            // Calculate amount
            $entryTime = new DateTime($booking['entry_time']);
            $expiryTime = new DateTime($booking['expiry_time']);
            $duration = $entryTime->diff($expiryTime);
            $hours = $duration->h + ($duration->days * 24);
            if ($hours < 1) $hours = 1;
            
            $amount = $hours * $booking['price_per_hour'];
            
            // Update booking with payment information
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET amount_paid = ?, payment_method = ?, payment_status = 'completed', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$amount, $data['paymentMethod'], $booking['id']]);
            
            return [
                'success' => true,
                'bookingId' => $booking['id']
            ];
        } catch (Exception $e) {
            error_log("PaymentController::processPayment - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing payment'
            ];
        }
    }
    
    /**
     * Get ticket information
     */
    public function getTicket($bookingId) {
        try {
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("
                SELECT b.*, v.vehicle_number, s.slot_number
                FROM bookings b
                JOIN vehicles v ON b.vehicle_id = v.id
                JOIN slots s ON b.slot_id = s.id
                WHERE b.id = ?
            ");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if ($booking) {
                $ticket = [
                    'ticket_id' => 'TKT-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT),
                    'vehicle_number' => $booking['vehicle_number'],
                    'slot_number' => $booking['slot_number'],
                    'entry_time' => $booking['entry_time'],
                    'expiry_time' => $booking['expiry_time'],
                    'amount_paid' => $booking['amount_paid']
                ];
                
                return [
                    'success' => true,
                    'ticket' => $ticket
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Ticket not found'
                ];
            }
        } catch (Exception $e) {
            error_log("PaymentController::getTicket - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving ticket information'
            ];
        }
    }
    
    /**
     * Calculate exit charges
     */
    public function calculateExitCharges($bookingId = null, $vehicleNumber = null) {
        try {
            $pdo = getDBConnection();
            
            if ($bookingId) {
                $stmt = $pdo->prepare("
                    SELECT b.*, v.vehicle_number, s.slot_number, s.price_per_hour
                    FROM bookings b
                    JOIN vehicles v ON b.vehicle_id = v.id
                    JOIN slots s ON b.slot_id = s.id
                    WHERE b.id = ? AND b.exit_time IS NULL
                ");
                $stmt->execute([$bookingId]);
            } else {
                $stmt = $pdo->prepare("
                    SELECT b.*, v.vehicle_number, s.slot_number, s.price_per_hour
                    FROM bookings b
                    JOIN vehicles v ON b.vehicle_id = v.id
                    JOIN slots s ON b.slot_id = s.id
                    WHERE v.vehicle_number = ? AND b.exit_time IS NULL
                    ORDER BY b.created_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$vehicleNumber]);
            }
            
            $booking = $stmt->fetch();
            
            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'No active booking found'
                ];
            }
            
            // Calculate charges
            $entryTime = new DateTime($booking['entry_time']);
            $exitTime = new DateTime();
            $duration = $entryTime->diff($exitTime);
            
            $totalHours = $duration->h + ($duration->days * 24) + ($duration->i / 60);
            if ($totalHours < 1) $totalHours = 1;
            
            $totalAmount = ceil($totalHours) * $booking['price_per_hour'];
            $paidAmount = $booking['amount_paid'] ?? 0;
            $additionalCharges = max(0, $totalAmount - $paidAmount);
            
            return [
                'success' => true,
                'exit' => [
                    'booking_id' => $booking['id'],
                    'vehicle_number' => $booking['vehicle_number'],
                    'slot_number' => $booking['slot_number'],
                    'entry_time' => $booking['entry_time'],
                    'exit_time' => $exitTime->format('Y-m-d H:i:s'),
                    'duration' => floor($totalHours) . ' hours ' . ($duration->i) . ' minutes',
                    'total_amount' => number_format($totalAmount, 2),
                    'additional_charges' => number_format($additionalCharges, 2)
                ]
            ];
        } catch (Exception $e) {
            error_log("PaymentController::calculateExitCharges - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while calculating exit charges'
            ];
        }
    }
    
    /**
     * Confirm exit and free slot
     */
    public function confirmExit($bookingId) {
        try {
            $pdo = getDBConnection();
            
            $pdo->beginTransaction();
            
            try {
                // Get booking and slot
                $stmt = $pdo->prepare("SELECT slot_id FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch();
                
                if (!$booking) {
                    throw new Exception('Booking not found');
                }
                
                // Update booking with exit time
                $stmt = $pdo->prepare("
                    UPDATE bookings 
                    SET exit_time = NOW(), updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$bookingId]);
                
                // Free the slot
                $stmt = $pdo->prepare("UPDATE slots SET status = 'available' WHERE id = ?");
                $stmt->execute([$booking['slot_id']]);
                
                $pdo->commit();
                
                return [
                    'success' => true
                ];
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log("PaymentController::confirmExit - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while confirming exit'
            ];
        }
    }
    
    /**
     * Extend parking time
     */
    public function extendParking($bookingId, $hours, $amount) {
        try {
            $pdo = getDBConnection();
            
            // Get current booking
            $stmt = $pdo->prepare("SELECT expiry_time FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                error_log("Booking not found for ID: {$bookingId}");
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }
            
            // Calculate new expiry time
            $currentExpiry = new DateTime($booking['expiry_time']);
            $newExpiry = clone $currentExpiry;
            $newExpiry->modify("+{$hours} hours");
            
            error_log("Extending booking {$bookingId} by {$hours} hours, from {$booking['expiry_time']} to {$newExpiry->format('Y-m-d H:i:s')}");
            
            // Update booking
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET expiry_time = ?, amount_paid = amount_paid + ?, updated_at = NOW()
                WHERE id = ?
            ");
            $updateResult = $stmt->execute([$newExpiry->format('Y-m-d H:i:s'), $amount, $bookingId]);
            
            if (!$updateResult) {
                error_log("Failed to update booking {$bookingId}");
                return [
                    'success' => false,
                    'message' => 'Failed to update booking'
                ];
            }
            
            // Get updated booking
            $stmt = $pdo->prepare("
                SELECT b.*, v.vehicle_number, s.slot_number
                FROM bookings b
                JOIN vehicles v ON b.vehicle_id = v.id
                JOIN slots s ON b.slot_id = s.id
                WHERE b.id = ?
            ");
            $stmt->execute([$bookingId]);
            $updatedBooking = $stmt->fetch();
            
            error_log("Booking {$bookingId} extended successfully");
            
            return [
                'success' => true,
                'booking' => $updatedBooking
            ];
        } catch (Exception $e) {
            error_log("PaymentController::extendParking - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while extending parking time'
            ];
        }
    }
    
    /**
     * Generate UPI QR data and process payment
     * Creates parking entry, generates UPI QR code, and marks payment as completed
     */
    public function generateUPIQRAndStoreEntry($data) {
        try {
            $pdo = getDBConnection();
            
            // Validate required fields
            if (!isset($data['vehicleId']) || !isset($data['slotId']) || !isset($data['amount'])) {
                return [
                    'success' => false,
                    'message' => 'Vehicle ID, Slot ID, and Amount are required'
                ];
            }
            
            $pdo->beginTransaction();
            
            try {
                // Get vehicle and slot information
                $stmt = $pdo->prepare("
                    SELECT v.*, s.slot_number, s.price_per_hour
                    FROM vehicles v
                    CROSS JOIN slots s
                    WHERE v.id = ? AND s.id = ?
                ");
                $stmt->execute([$data['vehicleId'], $data['slotId']]);
                $info = $stmt->fetch();
                
                if (!$info) {
                    throw new Exception('Vehicle or Slot not found');
                }
                
                // Calculate entry and expiry times
                $entryTime = isset($data['entryTime']) ? new DateTime($data['entryTime']) : new DateTime();
                $hours = isset($data['hours']) ? (int)$data['hours'] : 1;
                $expiryTime = clone $entryTime;
                $expiryTime->modify("+{$hours} hours");
                
                // Create parking entry
                $stmt = $pdo->prepare("
                    INSERT INTO parking_entries (
                        vehicle_id, slot_id, entry_time, expiry_time,
                        amount_paid, payment_method, payment_status,
                        entry_gate, notes, created_at
                    ) VALUES (?, ?, ?, ?, ?, 'mobile', 'completed', ?, ?, NOW())
                ");
                
                $entryGate = $data['entryGate'] ?? 'Gate-A';
                $notes = $data['notes'] ?? 'UPI Payment - QR Generated';
                
                $stmt->execute([
                    $data['vehicleId'],
                    $data['slotId'],
                    $entryTime->format('Y-m-d H:i:s'),
                    $expiryTime->format('Y-m-d H:i:s'),
                    $data['amount'],
                    $entryGate,
                    $notes
                ]);
                
                $entryId = $pdo->lastInsertId();
                
                // Also insert into bookings table for backward compatibility
                $stmt = $pdo->prepare("
                    INSERT INTO bookings (
                        vehicle_id, slot_id, entry_time, expiry_time,
                        amount_paid, payment_method, payment_status,
                        entry_gate, notes, created_at
                    ) VALUES (?, ?, ?, ?, ?, 'mobile', 'completed', ?, ?, NOW())
                ");
                $stmt->execute([
                    $data['vehicleId'],
                    $data['slotId'],
                    $entryTime->format('Y-m-d H:i:s'),
                    $expiryTime->format('Y-m-d H:i:s'),
                    $data['amount'],
                    $entryGate,
                    $notes
                ]);
                
                // Mark slot as occupied
                $stmt = $pdo->prepare("UPDATE slots SET status = 'occupied', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$data['slotId']]);
                
                // Generate UPI QR data
                $upiQRData = $this->generateUPIQRData([
                    'amount' => $data['amount'],
                    'transactionId' => 'PARK-' . str_pad($entryId, 8, '0', STR_PAD_LEFT),
                    'payeeName' => $data['payeeName'] ?? 'Smart Parking System',
                    'upiId' => $data['upiId'] ?? 'parking@paytm', // Default UPI ID, should be configurable
                    'transactionNote' => 'Parking Fee - ' . $info['slot_number'] . ' - ' . $info['vehicle_number']
                ]);
                
                $pdo->commit();
                
                // Get created entry details
                $stmt = $pdo->prepare("
                    SELECT pe.*, v.vehicle_number, v.owner_name, s.slot_number, s.price_per_hour
                    FROM parking_entries pe
                    JOIN vehicles v ON pe.vehicle_id = v.id
                    JOIN slots s ON pe.slot_id = s.id
                    WHERE pe.id = ?
                ");
                $stmt->execute([$entryId]);
                $entry = $stmt->fetch();
                
                return [
                    'success' => true,
                    'message' => 'Parking entry created and payment completed',
                    'entry_id' => $entryId,
                    'qr_data' => $upiQRData,
                    'entry' => [
                        'id' => $entry['id'],
                        'vehicle_number' => $entry['vehicle_number'],
                        'slot_number' => $entry['slot_number'],
                        'entry_time' => $entry['entry_time'],
                        'expiry_time' => $entry['expiry_time'],
                        'amount_paid' => $entry['amount_paid'],
                        'payment_status' => $entry['payment_status']
                    ]
                ];
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("PaymentController::generateUPIQRAndStoreEntry - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing payment: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Process extension payment
     */
    public function processExtensionPayment($data) {
        try {
            // Validate required fields
            if (!isset($data['bookingId']) || !isset($data['hours']) || !isset($data['amount'])) {
                return [
                    'success' => false,
                    'message' => 'BookingId, hours, and amount are required'
                ];
            }
            
            $bookingId = $data['bookingId'];
            $hours = (int)$data['hours'];
            $amount = (float)$data['amount'];
            
            // Call existing extendParking method
            $result = $this->extendParking($bookingId, $hours, $amount);
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'extensionId' => $bookingId,
                    'message' => 'Extension payment processed successfully'
                ];
            } else {
                return $result;
            }
        } catch (Exception $e) {
            error_log("PaymentController::processExtensionPayment - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing extension payment'
            ];
        }
    }
    
    /**
     * Generate UPI QR code data string
     * Format: upi://pay?pa=<UPI_ID>&pn=<PAYEE_NAME>&am=<AMOUNT>&cu=<CURRENCY>&tn=<TRANSACTION_NOTE>&tr=<TRANSACTION_ID>
     */
    private function generateUPIQRData($params) {
        $upiId = urlencode($params['upiId']);
        $payeeName = urlencode($params['payeeName']);
        $amount = number_format((float)$params['amount'], 2, '.', '');
        $currency = $params['currency'] ?? 'INR';
        $transactionNote = urlencode($params['transactionNote']);
        $transactionId = urlencode($params['transactionId']);
        
        // Build UPI payment URL
        $upiURL = sprintf(
            'upi://pay?pa=%s&pn=%s&am=%s&cu=%s&tn=%s&tr=%s',
            $upiId,
            $payeeName,
            $amount,
            $currency,
            $transactionNote,
            $transactionId
        );
        
        return [
            'upi_url' => $upiURL,
            'qr_data' => $upiURL, // Same data for QR code generation
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => $params['transactionId'],
            'payee_name' => $params['payeeName'],
            'upi_id' => $params['upiId'],
            'transaction_note' => $params['transactionNote']
        ];
    }
}

