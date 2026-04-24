export const getMedicalUniversities = () => {
    return [
        { name: 'Osh State Medical University', code: 'OSM-IMF' },
        { name: 'Kyrgyz State Medical Academy', code: 'KSMA' },
        { name: 'International School of Medicine', code: 'ISM' },
        { name: 'Ala-Too American University', code: 'ATAU' },
        { name: 'Kyrgyz National University', code: 'KNU' },
        { name: 'Alatoo International University', code: 'AIU' },
        { name: 'University of Central Asia', code: 'UCA' },
        { name: 'Bishkek University of Humanities', code: 'BUH' },
        { name: 'Kyrgyz-Turkish Manas University', code: 'KTMU' },
        { name: 'American University of Central Asia', code: 'AUCA' },
        { name: 'Issyk-Kul International University', code: 'IKIU' },

    ];
};

export const getMedicalCourses = () => {
    return [
        { name: 'Bachelor of Medicine', code: 'BM' },
        { name: 'Master of Surgery', code: 'MS' },
        { name: 'Doctor of Medicine (MD)', code: 'MD' },
        { name: 'MD-PhD Program', code: 'MDPHD' },
        { name: 'Clinical Research', code: 'CR' },
        { name: 'Medical Biochemistry', code: 'MB' },
        { name: 'Pharmacology', code: 'PHARM' },
        { name: 'Pathology', code: 'PATH' },
        { name: 'Internal Medicine', code: 'IM' },
        { name: 'Surgery', code: 'SURG' },
        { name: 'Pediatrics', code: 'PED' },
        { name: 'Cardiology', code: 'CARD' },
        { name: 'Neurology', code: 'NEURO' },
        { name: 'Psychiatry', code: 'PSYCH' }
    ];
};

export const getUniversityCourses = (universityName) => {
    const courseMap = {
        'KSMA': [
            { name: 'Bachelor of Medicine', code: 'BM' },
            { name: 'Master of Surgery', code: 'MS' },
            { name: 'Clinical Research', code: 'CR' }
        ],
        'ISM': [
            { name: 'Bachelor of Medicine', code: 'BM' },
            { name: 'Doctor of Medicine (MD)', code: 'MD' },
            { name: 'Medical Biochemistry', code: 'MB' },
            { name: 'Pharmacology', code: 'PHARM' }
        ],
        'ATAU': [
            { name: 'Bachelor of Medicine', code: 'BM' },
            { name: 'Doctor of Medicine (MD)', code: 'MD' },
            { name: 'MD-PhD Program', code: 'MDPHD' },
            { name: 'Internal Medicine', code: 'IM' }
        ],
        'KNU': [
            { name: 'Bachelor of Medicine', code: 'BM' },
            { name: 'Pathology', code: 'PATH' },
            { name: 'Surgery', code: 'SURG' },
            { name: 'Pediatrics', code: 'PED' }
        ],
        'AUCA': [
            { name: 'Bachelor of Medicine', code: 'BM' },
            { name: 'Cardiology', code: 'CARD' },
            { name: 'Neurology', code: 'NEURO' },
            { name: 'Psychiatry', code: 'PSYCH' }
        ],
        'OSM-IMF': [
            { name: 'Bachelor of Medicine', code: 'BM' },
            { name: 'Doctor of Medicine (MD)', code: 'MD' },
            { name: 'Clinical Research', code: 'CR' },
            { name: 'Medical Biochemistry', code: 'MB' },
            { name: 'Master of Surgery', code: 'MS' },
            { name: 'MD-PhD Program', code: 'MDPHD' },
            { name: 'Pharmacology', code: 'PHARM' },
            { name: 'Pathology', code: 'PATH' },
            { name: 'Internal Medicine', code: 'IM' },
            { name: 'Surgery', code: 'SURG' },
            { name: 'Pediatrics', code: 'PED' },
            { name: 'Cardiology', code: 'CARD' },
            { name: 'Neurology', code: 'NEURO' },
            { name: 'Psychiatry', code: 'PSYCH' },
            { name: 'Radiology', code: 'RAD' },
            { name: 'Dermatology', code: 'DERM' }
        ]
    };
    return courseMap || [];
};


export const getMedicalDepartments = () => {
    return [
        { name: 'Anatomy', code: 'ANAT' },
        { name: 'Physiology', code: 'PHYS' },
        { name: 'Biochemistry', code: 'BIOC' },
        { name: 'Pathology', code: 'PATH' },
        { name: 'Pharmacology', code: 'PHARM' },
        { name: 'Microbiology', code: 'MICRO' },
        { name: 'Internal Medicine', code: 'IM' },
        { name: 'Surgery', code: 'SURG' },
        { name: 'Pediatrics', code: 'PED' },
        { name: 'Obstetrics & Gynecology', code: 'OBG' },
        { name: 'Cardiology', code: 'CARD' },
        { name: 'Neurology', code: 'NEURO' },
        { name: 'Psychiatry', code: 'PSYCH' },
        { name: 'Radiology', code: 'RAD' },
        { name: 'Dermatology', code: 'DERM' }
    ];
};

export const getCountries = () => {
    return [
        { name: 'Kyrgyzstan', code: 'KG' },
        { name: 'United States', code: 'US' },
        { name: 'Canada', code: 'CA' },
        { name: 'United Kingdom', code: 'GB' },
        { name: 'Australia', code: 'AU' },
        { name: 'Germany', code: 'DE' },
        { name: 'France', code: 'FR' },
        { name: 'Japan', code: 'JP' },
        { name: 'India', code: 'IN' },
        { name: 'China', code: 'CN' }
    ];
};