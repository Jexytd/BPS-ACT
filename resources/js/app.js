import './bootstrap';
import Alpine from 'alpinejs';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';

window.Alpine = Alpine;
window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    resourceTimelinePlugin,
};

Alpine.start();
