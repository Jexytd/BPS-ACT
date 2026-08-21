const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');

// Replace old class string with added touch-action: none;
c = c.replace(
    /class="absolute rounded-md p-1.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden pointer-events-auto"/g,
    'class="absolute rounded-md p-1.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden pointer-events-auto" style="touch-action: none;"'
);

// Fallback replace without pointer-events-auto just in case
c = c.replace(
    /class="absolute rounded-md p-1.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden"/g,
    'class="absolute rounded-md p-1.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden pointer-events-auto" style="touch-action: none;"'
);

fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('touch action added');
