const fs = require('fs');
const file = 'c:/Users/Gouang/Desktop/BPS ACT/resources/views/activities/calendar_test.blade.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Add draggable attributes to the event div
content = content.replace(
    /<div @click\.stop="openEventDetail\(evt\)" @pointerdown\.stop\n\s*class="absolute rounded-md p-1\.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden pointer-events-auto"/g,
    `<div @click.stop="openEventDetail(evt)" @pointerdown.stop
         draggable="true"
         @dragstart="startDragEvent($event, evt)"
         @dragend="endDragEvent($event)"
         class="absolute rounded-md p-1.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden pointer-events-auto"`
);

// Add opacity-50 if dragging
content = content.replace(
    /:class="\{'text-white shadow-sm': !evt\.isPastel, 'text-gray-800': evt\.isPastel\}"/g,
    `:class="{'text-white shadow-sm': !evt.isPastel, 'text-gray-800': evt.isPastel, 'opacity-50 ring-2 ring-blue-400': draggingEvent?.id === evt.id}"`
);

// 2. Add dragover and drop to the day column
content = content.replace(
    /<div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white"\s*@pointerdown\.prevent="startSelection\(\$event, dayObj\.dateString\)">/g,
    `<div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white" 
         @pointerdown.prevent="startSelection($event, dayObj.dateString)"
         @dragover.prevent="dragOver($event)"
         @drop.prevent="dropEvent($event, dayObj.dateString)">`
);

// 3. Add draggingEvent state
content = content.replace(
    /isSelecting: false,/g,
    `draggingEvent: null,\n            isSelecting: false,`
);

// 4. Add drag functions after endSelection
const dragFunctions = `
            // --- EVENT DRAG & DROP LOGIC ---
            startDragEvent(event, evt) {
                this.draggingEvent = evt;
                event.dataTransfer.effectAllowed = 'move';
            },

            endDragEvent(event) {
                this.draggingEvent = null;
            },

            dragOver(event) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
            },

            async dropEvent(event, dateString) {
                event.preventDefault();
                if (!this.draggingEvent) return;

                const evt = this.draggingEvent;
                const droppedMinutes = this.getTimeFromPointer(event, event.currentTarget);
                
                // Calculate new start and end times
                const durationMinutes = evt.endMinutes - evt.startMinutes;
                
                const startHour = Math.floor(droppedMinutes / 60);
                const startMin = droppedMinutes % 60;
                
                const endMinutes = droppedMinutes + durationMinutes;
                const endHour = Math.floor(endMinutes / 60);
                const endMin = endMinutes % 60;

                const startTimeStr = String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0');
                const endTimeStr = String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0');

                // Adjust to the new date as well
                const newStart = \`\${dateString}T\${startTimeStr}\`;
                const newEnd = \`\${dateString}T\${endTimeStr}\`;

                // Optimistically update the UI
                const originalStart = evt.start;
                const originalEnd = evt.end;

                evt.start = newStart;
                evt.end = newEnd;
                
                const updatedEvt = this.processEventFormat(evt);
                const index = this.allEvents.findIndex(e => e.id === evt.id);
                if (index !== -1) {
                    this.allEvents[index] = updatedEvt;
                }
                
                this.draggingEvent = null;

                // Call API
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
                        this.fetchEvents(); // revert
                    } else {
                        this.fetchEvents(); // refresh from db
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan saat memindahkan kegiatan.');
                    this.fetchEvents(); // revert
                }
            },
`;

content = content.replace(
    /return \`top: \$\{topPx\}px; height: \$\{heightPx\}px;\`;\n\s*\},/g,
    `return \`top: \$\{topPx\}px; height: \$\{heightPx\}px;\`;
            },
${dragFunctions}`
);

fs.writeFileSync(file, content);
console.log('Drag and Drop feature added');
