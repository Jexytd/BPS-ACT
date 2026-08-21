const fs = require('fs');
const file = 'c:/Users/Gouang/Desktop/BPS ACT/resources/views/activities/calendar_test.blade.php';
let content = fs.readFileSync(file, 'utf8');

// 1. Remove HTML5 Drag and Drop stuff
content = content.replace(/draggable="true"\s*@dragstart="startDragEvent\(\$event, evt\)"\s*@dragend="endDragEvent\(\$event\)"/g, '');
content = content.replace(/@dragover\.prevent="dragOver\(\$event\)"\s*@drop\.prevent="dropEvent\(\$event, dayObj\.dateString\)"/g, '');

// 2. Add pointerdown event to event card and hide if dragging
content = content.replace(
    /<div @click\.stop="openEventDetail\(evt\)" @pointerdown\.stop\n\s*class="absolute/g,
    `<div @click.stop="openEventDetail(evt)" @pointerdown.stop="startEventDrag($event, evt, dayObj.dateString)"\n         class="absolute`
);
// Revert the opacity-50 ring-2 ring-blue-400 and replace with opacity-0 or opacity-20
content = content.replace(
    /:class="\{'text-white shadow-sm': !evt\.isPastel, 'text-gray-800': evt\.isPastel, 'opacity-50 ring-2 ring-blue-400': draggingEvent\?\.id === evt\.id\}"/g,
    `:class="{'text-white shadow-sm': !evt.isPastel, 'text-gray-800': evt.isPastel, 'opacity-30': isDraggingCard && draggedEvent?.id === evt.id}"`
);

// 3. Add data-date to day column
content = content.replace(
    /<div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white" \n\s*@pointerdown\.prevent="startSelection\(\$event, dayObj\.dateString\)">/g,
    `<div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white" \n                                     :data-date="dayObj.dateString"\n                                     @pointerdown.prevent="startSelection($event, dayObj.dateString)">`
);
content = content.replace( // Fallback if previous replacement didn't perfectly match (whitespace differences)
    /<div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white"\s*@pointerdown\.prevent="startSelection\(\$event, dayObj\.dateString\)">/g,
    `<div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white" \n                                     :data-date="dayObj.dateString"\n                                     @pointerdown.prevent="startSelection($event, dayObj.dateString)">`
);

// 4. Update window listeners on main
content = content.replace(
    /@pointermove\.window="updateSelection\(\$event\)"\n\s*@pointerup\.window="endSelection\(\$event\)"/g,
    `@pointermove.window="updateSelection($event); updateEventDrag($event);"\n              @pointerup.window="endSelection($event); endEventDrag($event);"`
);

// 5. Add Placeholder visualizer inside day column overlaid events loop
const placeholderHTML = `
                                        <!-- Placeholder Visualizer (Real-time drag) -->
                                        <template x-if="isDraggingCard && dragPlaceholder && dragPlaceholder.dateString === dayObj.dateString">
                                            <div class="absolute rounded-md p-1.5 flex flex-col overflow-hidden pointer-events-none z-50 text-white shadow-lg ring-2 ring-blue-500 scale-[1.02] transition-transform duration-75 opacity-90"
                                                 :style="getPlaceholderStyles()">
                                                <div class="font-semibold text-xs leading-tight truncate" x-text="dragPlaceholder.title"></div>
                                                <div class="text-[11px] opacity-90 leading-tight truncate mt-0.5 font-medium" 
                                                     x-text="formatDragPlaceholderTime()"></div>
                                            </div>
                                        </template>
                                        
                                        <!-- Event Cards di Day Column -->`;
content = content.replace(/<!-- Event Cards di Day Column -->/g, placeholderHTML);


// 6. Replace old drag functions with new pointer drag functions
const newDragFunctions = `
            // --- EVENT POINTER DRAG LOGIC (Interactive) ---
            isDraggingCard: false,
            draggedEvent: null,
            dragPlaceholder: null,
            dragPointerOffset: 0,

            startEventDrag(event, evt, dateString) {
                this.isDraggingCard = true;
                this.draggedEvent = evt;
                
                const rect = event.currentTarget.getBoundingClientRect();
                const pointerY = event.clientY - rect.top;
                this.dragPointerOffset = Math.floor(pointerY * (60 / this.hourHeight));

                const durationMinutes = evt.endMinutes - evt.startMinutes;

                this.dragPlaceholder = {
                    ...evt,
                    dateString: dateString,
                    startMinutes: evt.startMinutes,
                    endMinutes: evt.endMinutes,
                    durationMinutes: durationMinutes
                };

                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'grabbing';
            },

            updateEventDrag(event) {
                if (!this.isDraggingCard || !this.dragPlaceholder) return;

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
                let targetEndMinutes = targetStartMinutes + this.dragPlaceholder.durationMinutes;
                if (targetEndMinutes > 1440) {
                    targetEndMinutes = 1440;
                    targetStartMinutes = 1440 - this.dragPlaceholder.durationMinutes;
                }

                this.dragPlaceholder.dateString = dateString;
                this.dragPlaceholder.startMinutes = targetStartMinutes;
                this.dragPlaceholder.endMinutes = targetEndMinutes;
            },

            async endEventDrag(event) {
                if (!this.isDraggingCard) return;

                document.body.style.userSelect = '';
                document.body.style.cursor = '';

                const p = this.dragPlaceholder;
                const evt = this.draggedEvent;
                
                this.isDraggingCard = false;
                this.draggedEvent = null;
                this.dragPlaceholder = null;

                if (!p || (p.dateString === evt.startStr && p.startMinutes === evt.startMinutes)) {
                    return; 
                }

                const startHour = Math.floor(p.startMinutes / 60);
                const startMin = p.startMinutes % 60;
                const endHour = Math.floor(p.endMinutes / 60);
                const endMin = p.endMinutes % 60;

                const startTimeStr = String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0');
                const endTimeStr = String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0');

                const newStart = \`\${p.dateString}T\${startTimeStr}\`;
                const newEnd = \`\${p.dateString}T\${endTimeStr}\`;

                evt.start = newStart;
                evt.end = newEnd;
                const updatedEvt = this.processEventFormat(evt);
                const index = this.allEvents.findIndex(e => e.id === evt.id);
                if (index !== -1) {
                    this.allEvents[index] = updatedEvt;
                }

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
                        // Optimistic success, optionally refresh
                        this.fetchEvents(); 
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan saat memindahkan kegiatan.');
                    this.fetchEvents(); 
                }
            },

            getPlaceholderStyles() {
                const p = this.dragPlaceholder;
                if (!p) return 'display: none;';
                const topPx = (p.startMinutes / 60) * this.hourHeight;
                const heightPx = (p.durationMinutes / 60) * this.hourHeight;
                let bg = p.bgColor || '#005AA9';
                
                // Overlap column support (use original left & width)
                const left = p.leftPercent || 0;
                const widthPercent = p.widthPercent || 100;
                
                return \`top: \${topPx}px; height: \${heightPx}px; left: calc(\${left}% + 1px); width: calc(\${widthPercent}% - 2px); background-color: \${bg};\`;
            },

            formatDragPlaceholderTime() {
                const p = this.dragPlaceholder;
                if (!p) return '';
                const startHour = String(Math.floor(p.startMinutes / 60)).padStart(2, '0');
                const startMin = String(p.startMinutes % 60).padStart(2, '0');
                const endHour = String(Math.floor(p.endMinutes / 60)).padStart(2, '0');
                const endMin = String(p.endMinutes % 60).padStart(2, '0');
                return \`\${startHour}:\${startMin} - \${endHour}:\${endMin}\`;
            },
`;

// Remove the old drag functions block
content = content.replace(/\/\/ --- EVENT DRAG & DROP LOGIC ---[\s\S]*?\} catch \(e\) \{[\s\S]*?this\.fetchEvents\(\); \/\/ revert\n\s*\}\n\s*\}/, newDragFunctions);


fs.writeFileSync(file, content);
console.log('Interactive drag added');
