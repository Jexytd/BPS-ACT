const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');

const regex = /this\.draggedEvent\.startStr = dateString;\s*this\.draggedEvent\.startMinutes = targetStartMinutes;\s*this\.draggedEvent\.endMinutes = targetEndMinutes;/g;

const replacement = `
                const startH = Math.floor(targetStartMinutes / 60);
                const startM = targetStartMinutes % 60;
                const endH = Math.floor(targetEndMinutes / 60);
                const endM = targetEndMinutes % 60;
                const startTimeStr = String(startH).padStart(2, '0') + ':' + String(startM).padStart(2, '0');
                const endTimeStr = String(endH).padStart(2, '0') + ':' + String(endM).padStart(2, '0');

                this.draggedEvent.startStr = dateString;
                this.draggedEvent.startMinutes = targetStartMinutes;
                this.draggedEvent.endMinutes = targetEndMinutes;
                this.draggedEvent.startTimeString = startTimeStr;
                this.draggedEvent.endTimeString = endTimeStr;
                this.draggedEvent.timeString = startTimeStr + ' - ' + endTimeStr;
`;

c = c.replace(regex, replacement);

fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('Fixed computed property overwrite');
