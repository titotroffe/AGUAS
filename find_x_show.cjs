const fs = require('fs');
const lines = fs.readFileSync('resources/views/laboratorio/index.blade.php', 'utf8').split('\n');
lines.forEach((line, i) => {
    if (line.includes('<div x-show=')) {
        console.log('---');
        for (let j = Math.max(0, i-7); j <= i; j++) {
            console.log((j+1) + ': ' + lines[j].replace('\r', ''));
        }
    }
});
