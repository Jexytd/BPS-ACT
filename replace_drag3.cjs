const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');

// 1. Remove the placeholder template
c = c.replace(/<!-- Placeholder Visualizer \(Real-time drag\) -->[\s\S]*?<\/template>/, '');

// 2. Remove opacity-30 from card
c = c.replace(/'opacity-30': isDraggingCard && draggedEvent\?\.id === evt\.id/g, "'ring-2 ring-blue-500 scale-[1.02] opacity-90 z-50': isDraggingCard && draggedEvent?.id === evt.id");

// 3. Replace the drag logic
const newDragLogic = `
            // --- EVENT POINTER DRAG LOGIC (Interactive) ---
            isDraggingCard: false,
            draggedEvent: null,
            dragPointerOffset: 0,
            originalDragEventState: null,

            startEventDrag(event, evt, dateString) {
                this.isDraggingCard = true;
                this.draggedEvent = evt;
                
                // Save original state in case of cancel/revert
                this.originalDragEventState = {
                    startStr: evt.startStr,
                    startMinutes: evt.startMinutes,
                    endMinutes: evt.endMinutes,
                    start: evt.start,
                    end: evt.end
                };

                const rect = event.currentTarget.getBoundingClientRect();
                const pointerY = event.clientY - rect.top;
                this.dragPointerOffset = Math.floor(pointerY * (60 / this.hourHeight));

                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'grabbing';
            },

            updateEventDrag(event) {
                if (!this.isDraggingCard || !this.draggedEvent) return;

                const el = document.elementFromPoint(event.clientX, event.clientY);
                if (!el) return;
                
                const dayCol = el.closest('[data-date]');
                if (!dayCol) return;

                const dateString = dayCol.getAttribute('data-date');
                const rect = dayCol.getBoundingClientRect();
                const y = event.clientY - rect.top;
                
                let rawMinutes = Math.floor(y * (60 / this.hourHeight));
                let targetStartMinutes = rawMinutes - this.dragPointerOffset;
                targetStartMinutes = Math.round(targetStartMinutes / this.SLOT_MINUTES) * this.SLOT_MINUTES;

                if (targetStartMinutes < 0) targetStartMinutes = 0;
                
                const durationMinutes = this.originalDragEventState.endMinutes - this.originalDragEventState.startMinutes;
                let targetEndMinutes = targetStartMinutes + durationMinutes;
                
                if (targetEndMinutes > 1440) {
                    targetEndMinutes = 1440;
                    targetStartMinutes = 1440 - durationMinutes;
                }

                // Mutate the actual event to visually move it
                this.draggedEvent.startStr = dateString;
                this.draggedEvent.startMinutes = targetStartMinutes;
                this.draggedEvent.endMinutes = targetEndMinutes;
                this.draggedEvent.zIndex = 50; // bring to front while dragging
            },

            async endEventDrag(event) {
                if (!this.isDraggingCard || !this.draggedEvent) return;

                document.body.style.userSelect = '';
                document.body.style.cursor = '';

                const evt = this.draggedEvent;
                const orig = this.originalDragEventState;
                
                this.isDraggingCard = false;
                this.draggedEvent = null;

                // Did it change?
                if (orig.startStr === evt.startStr && orig.startMinutes === evt.startMinutes) {
                    return; // No change
                }

                const startHour = Math.floor(evt.startMinutes / 60);
                const startMin = evt.startMinutes % 60;
                const endHour = Math.floor(evt.endMinutes / 60);
                const endMin = evt.endMinutes % 60;

                const startTimeStr = String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0');
                const endTimeStr = String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0');

                const newStart = \`\${evt.startStr}T\${startTimeStr}\`;
                const newEnd = \`\${evt.startStr}T\${endTimeStr}\`;

                evt.start = newStart;
                evt.end = newEnd;

                // Clean up drag styles
                delete evt.zIndex;

                try {
                    const res = await fetch(\`/api/activities/\${evt.id}\`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            start: newStart,
                            end: newEnd
                        })
                    });

                    if (!res.ok) {
                        alert('Gagal memindahkan kegiatan.');
                        this.fetchEvents(); 
                    } else {
                        // Refresh to sync layout formatting with others
                        this.fetchEvents();
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan saat memindahkan kegiatan.');
                    this.fetchEvents(); 
                }
            },
`;

// Replace old drag logic blocks
c = c.replace(/\/\/ --- EVENT POINTER DRAG LOGIC[\s\S]*?async endEventDrag\(event\) \{[\s\S]*?this\.fetchEvents\(\);\s*\}\s*catch \(e\) \{[\s\S]*?\}\s*\}/, newDragLogic);

// Remove getPlaceholderStyles and formatDragPlaceholderTime
c = c.replace(/getPlaceholderStyles\(\) \{[\s\S]*?\},/g, '');
c = c.replace(/formatDragPlaceholderTime\(\) \{[\s\S]*?\},/g, '');


fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('direct card drag added');
