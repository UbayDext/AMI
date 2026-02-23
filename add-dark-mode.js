const fs = require('fs');
const path = require('path');

const mapping = [
    // Backgrounds
    ['bg-white', 'bg-white dark:bg-gray-800'],
    ['bg-gray-50', 'bg-gray-50 dark:bg-gray-900/50'],
    ['bg-gray-100', 'bg-gray-100 dark:bg-gray-800/80'],
    ['bg-indigo-50/50', 'bg-indigo-50/50 dark:bg-indigo-900/20'],
    ['bg-indigo-50', 'bg-indigo-50 dark:bg-indigo-900/40'],

    // Text
    ['text-gray-900', 'text-gray-900 dark:text-gray-100'],
    ['text-gray-800', 'text-gray-800 dark:text-gray-200'],
    ['text-gray-700', 'text-gray-700 dark:text-gray-300'],
    ['text-gray-600', 'text-gray-600 dark:text-gray-400'],
    ['text-gray-500', 'text-gray-500 dark:text-gray-400'],
    ['text-gray-400', 'text-gray-400 dark:text-gray-500'],
    ['text-indigo-700', 'text-indigo-700 dark:text-indigo-400'],
    ['text-indigo-800', 'text-indigo-800 dark:text-indigo-300'],
    ['text-indigo-900', 'text-indigo-900 dark:text-indigo-200'],

    // Borders
    ['border-gray-100', 'border-gray-100 dark:border-gray-700'],
    ['border-gray-200', 'border-gray-200 dark:border-gray-700'],
    ['border-gray-300', 'border-gray-300 dark:border-gray-600'],
    ['border-indigo-100', 'border-indigo-100 dark:border-indigo-900/50'],
    ['border-indigo-200', 'border-indigo-200 dark:border-indigo-700/50'],

    // Divides
    ['divide-gray-50', 'divide-gray-50 dark:divide-gray-800'],
    ['divide-gray-100', 'divide-gray-100 dark:divide-gray-800'],
    ['divide-gray-200', 'divide-gray-200 dark:divide-gray-700'],
];

function processDirectory(dir) {
    fs.readdirSync(dir).forEach(file => {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDirectory(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            let originalContent = content;

            // Simple regex replacement that tries to avoid double wrapping 
            // and negative classes if needed
            mapping.forEach(([light, dark]) => {
                // negative lookbehinds for avoiding matching things that already have dark styles, or partial matches like 'border-gray-1000'
                const regex = new RegExp(`(?<!dark:.*\\b)\\b${light.replace(/\//g, '\\/')}\\b`, 'g');
                // Avoid applying it again if the string already exists nearby
                content = content.replace(regex, (match, offset, string) => {
                    const substr = string.substring(offset, offset + dark.length);
                    if (substr === dark) return match; // already applied
                    return dark;
                });
            });

            if (content !== originalContent) {
                fs.writeFileSync(fullPath, content);
                console.log(`Updated ${fullPath}`);
            }
        }
    });
}

processDirectory('./resources/views');
console.log('Done!');
