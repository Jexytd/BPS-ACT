const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');

// 1. Add dragUpdateCount to state
c = c.replace(/isDraggingCard: false,/, 'isDraggingCard: false,\ndragUpdateCount: 0,');

// 2. Capture pointer in startEventDrag
c = c.replace(/document\.body\.style\.userSelect = 'none';\s*document\.body\.style\.cursor = 'grabbing';/, `
                event.currentTarget.setPointerCapture(event.pointerId);
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'grabbing';
                this.dragUpdateCount = 0;
`);

// 3. Make the dragged card pointer-events-none so elementFromPoint works correctly
c = c.replace(/'ring-2 ring-blue-500 scale-\[1\.02\] opacity-90 z-50': isDraggingCard && draggedEvent\?\.id === evt\.id/g, "'ring-2 ring-blue-500 scale-[1.02] opacity-90 z-50 pointer-events-none': isDraggingCard && draggedEvent?.id === evt.id");

// 4. Increment dragUpdateCount in updateEventDrag
c = c.replace(/const el = document\.elementFromPoint\(event\.clientX, event\.clientY\);/, `
                this.dragUpdateCount++;
                const el = document.elementFromPoint(event.clientX, event.clientY);
`);

// 5. Release pointer capture in endEventDrag
c = c.replace(/document\.body\.style\.userSelect = '';\s*document\.body\.style\.cursor = '';/, `
                if (event && event.currentTarget && event.currentTarget.releasePointerCapture) {
                    try { event.currentTarget.releasePointerCapture(event.pointerId); } catch(e){}
                }
                document.body.style.userSelect = '';
                document.body.style.cursor = '';
`);

// 6. Update debug panel to show dragUpdateCount
c = c.replace(/<div>StartStr: <span x-text="draggedEvent \? draggedEvent\.startStr : ''"><\/span><\/div>/, `<div>StartStr: <span x-text="draggedEvent ? draggedEvent.startStr : ''"></span></div>\n    <div>Updates: <span x-text="dragUpdateCount"></span></div>`);

fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('Fixed drag capture and hit testing');
