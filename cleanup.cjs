const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');
c = c.replace(/<!-- Placeholder Visualizer \(Real-time drag\) -->[\s\S]*?<\/template>/, '');
c = c.replace(/'opacity-30': isDraggingCard && draggedEvent\?\.id === evt\.id/g, "'ring-2 ring-blue-500 scale-[1.02] opacity-90 z-50': isDraggingCard && draggedEvent?.id === evt.id");
c = c.replace(/getPlaceholderStyles\(\)\s*\{[\s\S]*?\},/g, '');
c = c.replace(/formatDragPlaceholderTime\(\)\s*\{[\s\S]*?\},/g, '');
fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('Cleanup complete');
