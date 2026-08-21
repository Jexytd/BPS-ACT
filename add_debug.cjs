const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');

const debugHtml = `
<div class="fixed bottom-4 right-4 bg-black text-white p-4 rounded z-50 font-mono text-xs" style="pointer-events: none;">
    <div>Dragging: <span x-text="isDraggingCard"></span></div>
    <div>Dragged ID: <span x-text="draggedEvent ? draggedEvent.id : 'none'"></span></div>
    <div>Offset: <span x-text="dragPointerOffset"></span></div>
    <div>StartMins: <span x-text="draggedEvent ? draggedEvent.startMinutes : ''"></span></div>
    <div>StartStr: <span x-text="draggedEvent ? draggedEvent.startStr : ''"></span></div>
</div>
</main>`;

c = c.replace(/<\/main>/, debugHtml);
fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('Added debug panel');
