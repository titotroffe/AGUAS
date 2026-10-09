const fs = require('fs');
const lines = fs.readFileSync('resources/views/jefatura/index.blade.php', 'utf8').split('\n');
lines.forEach((line, i) => {
    if (line.includes('bg-slate-800/80') || line.includes('bg-slate-800/50')) {
        if(line.includes('flex justify-between items-center') || line.includes('border-b border-slate-700')) {
           console.log((i+1) + ': ' + line.trim());
        }
    }
});
