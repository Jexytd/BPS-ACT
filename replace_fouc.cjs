const fs = require('fs');
const file = 'c:/Users/Gouang/Desktop/BPS ACT/resources/views/layouts/app.blade.php';
let content = fs.readFileSync(file, 'utf8');

// Add inline script and style to head
const scriptAndStyle = `
    <script>
        // Prevent FOUC for sidebar state
        if (localStorage.getItem('sidebarMinimized') === 'true') {
            document.documentElement.classList.add('sidebar-minimized-fouc');
        } else {
            document.documentElement.classList.add('sidebar-expanded-fouc');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* FOUC Prevention */
        html.sidebar-minimized-fouc #sidebar { width: 5rem !important; }
        html.sidebar-minimized-fouc #sidebar [x-show="!sidebarMinimized"] { display: none !important; }
        
        html.sidebar-expanded-fouc #sidebar { width: 16rem !important; }
    </style>
</head>
`;
content = content.replace('</head>', scriptAndStyle);

// Add id="sidebar" to aside if not exists
if (!content.includes('id="sidebar"')) {
    content = content.replace('<aside ', '<aside id="sidebar" ');
}

// Ensure body clears the fouc classes on init
content = content.replace(
    'init() {',
    'init() {\n        document.documentElement.classList.remove(\'sidebar-minimized-fouc\', \'sidebar-expanded-fouc\');'
);

fs.writeFileSync(file, content);
console.log('Added FOUC prevention');
