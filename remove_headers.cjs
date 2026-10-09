const fs = require('fs');

const files = [
    'resources/views/quimico/index.blade.php',
    'resources/views/operadores/index.blade.php',
    'resources/views/laboratorio/index.blade.php',
    'resources/views/jefatura/index.blade.php',
    'resources/views/components/panel-bombas.blade.php'
];

files.forEach(file => {
    if (fs.existsSync(file)) {
        let content = fs.readFileSync(file, 'utf8');
        // Replace regex: matching the div, then any whitespace/newline, then span with text-blue-400 and any content, then closing div
        const regex = /[ \t]*<div class="bg-slate-800\/80 p-6 flex justify-between items-center text-xl font-bold text-white tracking-wider border-b border-slate-700">\s*<span class="text-blue-400">.*?<\/span>\s*<\/div>\r?\n?/g;
        
        let newContent = content.replace(regex, '');
        fs.writeFileSync(file, newContent, 'utf8');
        console.log('Processed ' + file);
    }
});
