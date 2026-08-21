import './bootstrap';
import Alpine from 'alpinejs';
import { Calendar } from 'fullcalendar';
import dayGridPlugin from 'fullcalendar/daygrid';
import timeGridPlugin from 'fullcalendar/timegrid';
import interactionPlugin from 'fullcalendar/interaction';
import resourceTimelinePlugin from 'fullcalendar-scheduler/resource-timeline';
import formaThemePlugin from 'fullcalendar/themes/forma';
import idLocale from 'fullcalendar/locales/id';

import 'fullcalendar/skeleton.css';
import 'fullcalendar/themes/forma/theme.css';
import 'fullcalendar/themes/forma/palettes/blue.css';

window.Alpine = Alpine;
window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    resourceTimelinePlugin,
    formaThemePlugin,
    idLocale,
};

Alpine.start();
