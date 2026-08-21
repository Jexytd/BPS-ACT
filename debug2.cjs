const fs = require('fs');
let c = fs.readFileSync('resources/views/activities/calendar_test.blade.php', 'utf8');

c = c.replace(/dragUpdateCount: 0,/, "dragUpdateCount: 0, debugElClass: '', targetMinsDebug: 0,");

c = c.replace(/const el = document\.elementFromPoint\(event\.clientX, event\.clientY\);/, `const el = document.elementFromPoint(event.clientX, event.clientY);
if (el) this.debugElClass = el.className; else this.debugElClass = 'null';`);

c = c.replace(/this\.draggedEvent\.startMinutes = targetStartMinutes;/, `this.draggedEvent.startMinutes = targetStartMinutes;
this.targetMinsDebug = targetStartMinutes;`);

c = c.replace(/<div>Updates: <span x-text="dragUpdateCount"><\/span><\/div>/, `<div>Updates: <span x-text="dragUpdateCount"></span></div>
<div>Hit: <span x-text="debugElClass"></span></div>
<div>TargetMins: <span x-text="targetMinsDebug"></span></div>`);

fs.writeFileSync('resources/views/activities/calendar_test.blade.php', c);
console.log('Added more debug info');
