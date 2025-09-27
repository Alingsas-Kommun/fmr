#!/usr/bin/env node

import fs from 'fs';
import path from 'path';

const DOMAIN = 'fmr';
const HANDLE = 'app-js';
const LANG_DIR = './resources/lang';

// Helper functions
const getLocale = (filename) => filename.match(/([a-z]{2}_[A-Z]{2})/)?.[1];
const isTargetFile = (filename) => filename.endsWith('.json') && !filename.startsWith(`${DOMAIN}-`);
const createFilename = (locale) => `${DOMAIN}-${locale}-${HANDLE}.json`;

// Get all translation files that need processing
const filesToProcess = fs.readdirSync(LANG_DIR).filter(isTargetFile);

if (filesToProcess.length === 0) {
    console.log('No translation files to process.');
    process.exit(0);
}

// Group and merge translations by locale
const locales = new Map();

filesToProcess.forEach(filename => {
    const locale = getLocale(filename);
    if (!locale) return;

    try {
        const data = JSON.parse(fs.readFileSync(path.join(LANG_DIR, filename), 'utf8'));
        const domainData = Object.values(data.locale_data || {})[0] || {};
        
        // Extract translations (excluding metadata)
        const translations = Object.fromEntries(
            Object.entries(domainData).filter(([key]) => key !== "")
        );

        if (!locales.has(locale)) {
            locales.set(locale, { files: [], translations: {} });
        }

        const localeData = locales.get(locale);
        localeData.files.push(filename);
        Object.assign(localeData.translations, translations);

    } catch (error) {
        console.warn(`Skipping ${filename}: ${error.message}`);
    }
});

// Create merged files
locales.forEach(({ files, translations }, locale) => {
    const outputFile = createFilename(locale);
    
    const mergedData = {
        "domain": DOMAIN,
        "locale_data": {
            [DOMAIN]: {
                "": { "domain": DOMAIN, "lang": locale, "plural-forms": "nplurals=2; plural=(n != 1);" },
                ...translations
            }
        }
    };

    fs.writeFileSync(path.join(LANG_DIR, outputFile), JSON.stringify(mergedData, null, 2));
    console.log(`✓ ${outputFile} (${Object.keys(translations).length} strings from ${files.length} files)`);

    // Clean up old files
    files.forEach(file => fs.unlinkSync(path.join(LANG_DIR, file)));
});
