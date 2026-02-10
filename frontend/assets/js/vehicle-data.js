/**
 * Vehicle Companies and Models Database
 * Contains 10 car companies with 5 models each for enhanced security and vehicle tracking
 */

const vehicleCompanies = {
    'car': {
        'maruti': {
            name: '🇮🇳 Maruti Suzuki',
            models: ['Swift', 'Alto', 'Baleno', 'Vitara Brezza', 'Ertiga']
        },
        'hyundai': {
            name: '🇰🇷 Hyundai',
            models: ['i20', 'Creta', 'Venue', 'Verna', 'Tucson']
        },
        'tata': {
            name: '🇮🇳 Tata Motors',
            models: ['Nexon', 'Harrier', 'Altroz', 'Tigor', 'Safari']
        },
        'mahindra': {
            name: '🇮🇳 Mahindra',
            models: ['XUV500', 'Bolero', 'Scorpio', 'Marazzo', 'KUV100']
        },
        'honda': {
            name: '🇯🇵 Honda',
            models: ['City', 'Jazz', 'WR-V', 'Amaze', 'CR-V']
        },
        'toyota': {
            name: '🇯🇵 Toyota',
            models: ['Fortuner', 'Innova', 'Corolla', 'Glanza', 'Yaris']
        },
        'ford': {
            name: '🇺🇸 Ford',
            models: ['EcoSport', 'Figo', 'Aspire', 'Freestyle', 'Endeavour']
        },
        'kia': {
            name: '🇰🇷 Kia Motors',
            models: ['Seltos', 'Sonet', 'Niro', 'Carnival', 'EV9']
        },
        'skoda': {
            name: '🇨🇿 Skoda',
            models: ['Slavia', 'Superb', 'Kodiaq', 'Kushaq', 'Octavia']
        },
        'volkswagen': {
            name: '🇩🇪 Volkswagen',
            models: ['Polo', 'Vento', 'Tiguan', 'T-Cross', 'Taigun']
        }
    },
    'motorcycle': {
        'hero': {
            name: '🇮🇳 Hero MotoCorp',
            models: ['Splendor', 'Passion', 'HF Deluxe', 'Glamour', 'Xtreme']
        },
        'bajaj': {
            name: '🇮🇳 Bajaj Auto',
            models: ['Pulsar', 'CT100', 'Platina', 'Dominar', 'Avenger']
        },
        'honda_bike': {
            name: '🇯🇵 Honda Motorcycles',
            models: ['CB Shine', 'Unicorn', 'CBR 150R', 'CB200X', 'Hornet 2.0']
        },
        'tvs': {
            name: '🇮🇳 TVS',
            models: ['Apache', 'Radeon', 'Jupiter', 'Ntorq', 'Raider']
        },
        'royal_enfield': {
            name: '🇮🇳 Royal Enfield',
            models: ['Classic', 'Bullet', 'Himalayan', 'Interceptor', 'Continental']
        }
    },
    'truck': {
        'tata_truck': {
            name: '🇮🇳 Tata Trucks',
            models: ['Ace', 'Intra', 'Prima', 'Signa', 'LPT']
        },
        'ashok': {
            name: '🇮🇳 Ashok Leyland',
            models: ['Bada Dost', 'Guru', 'Boss', 'Captain', 'Partner']
        },
        'mahindra_truck': {
            name: '🇮🇳 Mahindra Trucks',
            models: ['Big Bolero', 'Maxximo', 'Supro', 'Quanto', 'TUV300']
        },
        'eicher': {
            name: '🇮🇳 Eicher',
            models: ['Pro 1050', 'Pro 1080', 'Pro 2090', 'Pro Truck', 'Cargo']
        },
        'volvo': {
            name: '🇸🇪 Volvo Trucks',
            models: ['FM', 'FH', 'FMX', 'FL', 'VNL']
        }
    },
    'van': {
        'toyota_van': {
            name: '🇯🇵 Toyota',
            models: ['Innova', 'Fortuner', 'Avanza', 'Hiace', 'Vellfire']
        },
        'mahindra_van': {
            name: '🇮🇳 Mahindra',
            models: ['Marazzo', 'Xylo', 'Quanto', 'Bolero Pik-Up', 'KUV100']
        },
        'force': {
            name: '🇮🇳 Force Motors',
            models: ['Traveller', 'Gurkha', 'Trax', 'C-Boot', 'Urbania']
        },
        'nissan_van': {
            name: '🇯🇵 Nissan',
            models: ['Evalia', 'NV350', 'Datsun Go', 'X-Trail', 'Teana']
        },
        'suzuki_van': {
            name: '🇯🇵 Suzuki',
            models: ['Ertiga', 'Wagon-R', 'Vitara', 'XL7', 'S-Presso']
        }
    },
    'electric': {
        'tesla': {
            name: '⚡ Tesla',
            models: ['Model 3', 'Model S', 'Model X', 'Model Y', 'Roadster']
        },
        'tata_ev': {
            name: '⚡ Tata Electric',
            models: ['Nexon EV', 'Tigor EV', 'Altroz EV', 'Harrier EV', 'Nexon EV Plus']
        },
        'mahindra_ev': {
            name: '⚡ Mahindra Electric',
            models: ['XUV400', 'e20 Plus', 'eKUV100', 'eXUV300', 'e-Verito']
        },
        'hyundai_ev': {
            name: '⚡ Hyundai Electric',
            models: ['Kona', 'Ioniq', 'e-Santa Fe', 'Ioniq 6', 'Ioniq 5']
        },
        'mg': {
            name: '⚡ MG Electric',
            models: ['ZS EV', 'Hector EV', 'Astor EV', 'Comet EV', 'eGLO']
        }
    }
};

// Get companies for a vehicle type
function getCompaniesForType(vehicleType) {
    if (vehicleCompanies[vehicleType]) {
        return vehicleCompanies[vehicleType];
    }
    return {};
}

// Get models for a company
function getModelsForCompany(vehicleType, companyKey) {
    if (vehicleCompanies[vehicleType] && vehicleCompanies[vehicleType][companyKey]) {
        return vehicleCompanies[vehicleType][companyKey].models;
    }
    return [];
}

// Check if company and model exist in database
function isLuxuryVehicle(vehicleType, companyKey, model) {
    const companies = vehicleCompanies[vehicleType];
    if (companies && companies[companyKey]) {
        const company = companies[companyKey];
        return company.models.includes(model);
    }
    return false;
}

// Get full vehicle info
function getVehicleInfo(vehicleType, companyKey, model) {
    const companies = vehicleCompanies[vehicleType];
    if (companies && companies[companyKey]) {
        const company = companies[companyKey];
        return {
            vehicleType: vehicleType,
            companyKey: companyKey,
            companyName: company.name,
            model: model,
            isRegistered: company.models.includes(model),
            securityLevel: company.models.includes(model) ? 'high' : 'standard'
        };
    }
    return null;
}
