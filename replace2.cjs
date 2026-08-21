const fs = require('fs');
const file = 'c:/Users/Gouang/Desktop/BPS ACT/resources/views/layouts/app.blade.php';
let content = fs.readFileSync(file, 'utf8');

// Use object syntax for aside
content = content.replace(
    /<aside :class="sidebarMinimized \? 'w-20' : 'w-64'" class="{{ \$isSidebarMinimized \? 'w-20' : 'w-64' }} {{ \$isSidebarMinimized \? 'w-20' : 'w-64' }} /g,
    '<aside :class="{ \'w-20\': sidebarMinimized, \'w-64\': !sidebarMinimized }" class="'
);
content = content.replace(
    /<aside :class="sidebarMinimized \? 'w-20' : 'w-64'" class="{{ \$isSidebarMinimized \? 'w-20' : 'w-64' }} /g,
    '<aside :class="{ \'w-20\': sidebarMinimized, \'w-64\': !sidebarMinimized }" class="'
);

// Use object syntax for nav links
content = content.replace(
    /:class="sidebarMinimized \? 'justify-center px-0' : 'gap-3 px-3'" class="{{ \$isSidebarMinimized \? 'justify-center px-0' : 'gap-3 px-3' }} /g,
    ':class="{ \'justify-center px-0\': sidebarMinimized, \'gap-3 px-3\': !sidebarMinimized }" class="'
);
content = content.replace(
    /:class="sidebarMinimized \? 'justify-center px-0' : 'px-5 gap-3'" class="{{ \$isSidebarMinimized \? 'justify-center px-0' : 'px-5 gap-3' }} /g,
    ':class="{ \'justify-center px-0\': sidebarMinimized, \'px-5 gap-3\': !sidebarMinimized }" class="'
);

// Use object syntax for user profile
content = content.replace(
    /:class="sidebarMinimized \? 'flex-col justify-center text-center p-2' : 'p-2 gap-2'"/g,
    ':class="{ \'flex-col justify-center text-center p-2\': sidebarMinimized, \'p-2 gap-2\': !sidebarMinimized }"'
);
content = content.replace(
    /:class="sidebarMinimized \? 'justify-center' : 'gap-2.5'"/g,
    ':class="{ \'justify-center\': sidebarMinimized, \'gap-2.5\': !sidebarMinimized }"'
);

// Form
content = content.replace(
    /:class="sidebarMinimized \? 'mt-2' : ''" class="{{ \$isSidebarMinimized \? 'mt-2' : '' }}"/g,
    ':class="{ \'mt-2\': sidebarMinimized }"'
);

// Remove the inline styles that were based on php
content = content.replace(/ style="{{ \$isSidebarMinimized \? 'display: none;' : '' }}"/g, '');

fs.writeFileSync(file, content);
console.log('Reverted to object syntax');
