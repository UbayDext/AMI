import fs from 'fs';
import path from 'path';

const mapping = [
    // Backgrounds
    ['bg-white', 'bg-white dark:bg-gray-800'],
    ['bg-gray-50', 'bg-gray-50 dark:bg-gray-900/50'],
    ['bg-gray-100', 'bg-gray-100 dark:bg-gray-800/80'],
    ['bg-gray-200', 'bg-gray-200 dark:bg-gray-700'],
    ['bg-indigo-50/50', 'bg-indigo-50/50 dark:bg-indigo-900/20'],
    ['bg-indigo-50', 'bg-indigo-50 dark:bg-indigo-900/40'],
    ['bg-indigo-100', 'bg-indigo-100 dark:bg-indigo-900/60'],

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
];

function processDirectory(dir) {
    fs.readdirSync(dir).forEach(file => {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDirectory(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            // Ignore layouts folder as we've already done app.blade.php and navigation.blade.php
            if (fullPath.includes('layouts\\')) return;
            if (fullPath.includes('layouts/')) return;

            let content = fs.readFileSync(fullPath, 'utf8');
            let originalContent = content;

            mapping.forEach(([light, dark]) => {
                const regex = new RegExp(`([^a-zA-Z0-9:-])(${light})([^a-zA-Z0-9:-])`, 'g');
                content = content.replace(regex, (match, p1, p2, p3) => {
                    // Check if the next part is already dark mode (basic heuristic)
                    const substr = originalContent.substring(originalContent.indexOf(match), originalContent.indexOf(match) + dark.length + 20);
                    if (substr.includes(dark)) return match;
                    return p1 + dark + p3;
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
console.log('Done mapping dark mode classes!');
