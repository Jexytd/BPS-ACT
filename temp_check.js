function googleCalendarApp() {
        return {
            loading: true,
            isSubmitting: false,
            sidebarOpen: false, // window.innerWidth > 640 (Disembunyikan sementara)
            
            // View State
            currentView: 'day', // 'year', 'month', 'week', 'day'
            
            // Hour Slot Height (60px per hour => 1px per minute)
            hourHeight: 60,
            
            // Dates state
            mainDate: new Date(),
            miniDate: new Date(),
            
            allEvents: [],
            showDetailModal: false,
            selectedEvent: null,
            showDayModal: false,
            selectedDayObj: null,
            showMyCals: true,
            
            // Current Time tracking
            nowTick: Date.now(),
            
            // Time Slot Selection state
            draggingEvent: null,
            isSelecting: false,
            selectionStart: null,
            selectionEnd: null,
            selectedDate: null,
            SLOT_MINUTES: 30,
            currentDragContainer: null,
            
            // Create Event Modal state
            showCreateModal: false,
            createForm: {
                title: '',
                category: 'Pekerjaan / Survei',
                start: '',
                end: '',
                allDay: false,
                location: '',
                description: '',
                status: 'planned',
                assignees: ['{{ $user['id'] ?? '' }}']
            },

            filters: { saya: true, pekerjaan: true, pribadi: true, pengingat: true, libur: true },
            
            MONTH_NAMES: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            DAY_NAMES: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],

            // Data Hari Libur Nasional Indonesia
            HOLIDAYS: {
                // 2025
                '2025-01-01': 'Tahun Baru 2025 Masehi',
                '2025-01-27': 'Isra Mi\'raj Nabi Muhammad SAW',
                '2025-01-29': 'Tahun Baru Imlek 2576 Kongzili',
                '2025-03-29': 'Hari Suci Nyepi Saka 1947',
                '2025-03-31': 'Hari Raya Idul Fitri 1446 H',
                '2025-04-01': 'Hari Raya Idul Fitri 1446 H',
                '2025-04-18': 'Wafat Yesus Kristus',
                '2025-04-20': 'Hari Paskah',
                '2025-05-01': 'Hari Buruh Internasional',
                '2025-05-12': 'Hari Raya Waisak 2569 BE',
                '2025-05-29': 'Kenaikan Yesus Kristus',
                '2025-06-01': 'Hari Lahir Pancasila',
                '2025-06-06': 'Hari Raya Idul Adha 1446 H',
                '2025-06-27': 'Tahun Baru Islam 1447 H',
                '2025-08-17': 'Hari Kemerdekaan RI ke-80',
                '2025-09-05': 'Maulid Nabi Muhammad SAW',
                '2025-12-25': 'Hari Raya Natal',

                // 2026
                '2026-01-01': 'Tahun Baru 2026 Masehi',
                '2026-01-16': 'Isra Mi\'raj Nabi Muhammad SAW',
                '2026-02-17': 'Tahun Baru Imlek 2577 Kongzili',
                '2026-03-20': 'Hari Suci Nyepi Saka 1948',
                '2026-03-21': 'Hari Raya Idul Fitri 1447 H',
                '2026-03-22': 'Hari Raya Idul Fitri 1447 H',
                '2026-04-03': 'Wafat Yesus Kristus',
                '2026-04-05': 'Hari Paskah',
                '2026-05-01': 'Hari Buruh Internasional',
                '2026-05-14': 'Kenaikan Yesus Kristus',
                '2026-05-27': 'Hari Raya Idul Adha 1447 H',
                '2026-05-31': 'Hari Raya Waisak 2570 BE',
                '2026-06-01': 'Hari Lahir Pancasila',
                '2026-06-16': 'Tahun Baru Islam 1448 H',
                '2026-08-17': 'Hari Kemerdekaan RI ke-81',
                '2026-08-25': 'Maulid Nabi Muhammad',
                '2026-12-25': 'Hari Raya Natal',

                // 2027
                '2027-01-01': 'Tahun Baru 2027 Masehi',
                '2027-01-05': 'Isra Mi\'raj Nabi Muhammad SAW',
                '2027-02-06': 'Tahun Baru Imlek 2578 Kongzili',
                '2027-03-09': 'Hari Suci Nyepi Saka 1949',
                '2027-03-10': 'Hari Raya Idul Fitri 1448 H',
                '2027-03-11': 'Hari Raya Idul Fitri 1448 H',
                '2027-03-26': 'Wafat Yesus Kristus',
                '2027-03-28': 'Hari Paskah',
                '2027-05-01': 'Hari Buruh Internasional',
                '2027-05-06': 'Kenaikan Yesus Kristus',
                '2027-05-16': 'Hari Raya Idul Adha 1448 H',
                '2027-05-20': 'Hari Raya Waisak 2571 BE',
                '2027-06-01': 'Hari Lahir Pancasila',
                '2027-06-06': 'Tahun Baru Islam 1449 H',
                '2027-08-15': 'Maulid Nabi Muhammad SAW',
                '2027-08-17': 'Hari Kemerdekaan RI ke-82',
                '2027-12-25': 'Hari Raya Natal'
            },

            initCalendar() {
                this.mainDate = new Date();
                this.miniDate = new Date(this.mainDate);
                this.fetchEvents();

                // Live timer update
                setInterval(() => {
                    this.nowTick = Date.now();
                }, 30000);

                // Auto-scroll to current hour on load
                this.scrollToCurrentTime();

                // Handle resize for sidebar
                window.addEventListener('resize', () => {
                    if (window.innerWidth < 640) this.sidebarOpen = false;
                });

                // Bersihkan efek visual seleksi (blok biru) saat modal pembuatan ditutup
                this.$watch('showCreateModal', (value) => {
                    if (!value) {
                        this.isSelecting = false;
                        this.selectedDate = null;
                    }
                });
            },

            openDayModal(dayObj, event) {
                this.selectedDayObj = dayObj;
                this.showDayModal = true;
                
                if (event) {
                    this.$nextTick(() => {
                        const modal = document.getElementById('dayEventsModal');
                        if (!modal) return;
                        
                        const rect = event.currentTarget.getBoundingClientRect();
                        const modalRect = modal.getBoundingClientRect();
                        
                        // Default position: to the right of the cell
                        let top = rect.top - 20;
                        let left = rect.right + 10; 

                        // If overflows right edge, put it to the left of the cell
                        if (left + modalRect.width > window.innerWidth) {
                            left = rect.left - modalRect.width - 10;
                        }
                        
                        // If overflows bottom edge, shift it up
                        if (top + modalRect.height > window.innerHeight) {
                            top = window.innerHeight - modalRect.height - 20;
                        }
                        
                        // Safety boundaries
                        if (top < 0) top = 20;
                        if (left < 0) left = 20;
                        
                        modal.style.top = top + 'px';
                        modal.style.left = left + 'px';
                        modal.style.transform = 'none'; // Clear centering transform
                    });
                } else {
                    // Fallback to center if no event is passed
                    this.$nextTick(() => {
                        const modal = document.getElementById('dayEventsModal');
                        if (modal) {
                            modal.style.top = '50%';
                            modal.style.left = '50%';
                            modal.style.transform = 'translate(-50%, -50%)';
                        }
                    });
                }
            },

            getHolidayForDate(dateString) {
                if (!this.filters.libur) return null;
                return this.HOLIDAYS[dateString] || null;
            },

            getViewLabel(view) {
                const labels = { 'year': 'Tahun', 'month': 'Bulan', 'week': 'Minggu', 'day': 'Hari' };
                return labels[view] || 'Hari';
            },

            get mainHeaderString() {
                if (this.currentView === 'year') {
                    return `${this.mainDate.getFullYear()}`;
                } else if (this.currentView === 'month' || this.currentView === 'week' || this.currentView === 'day') {
                    return `${this.MONTH_NAMES[this.mainDate.getMonth()]} ${this.mainDate.getFullYear()}`;
                }
            },

            get miniMonthYearString() {
                return `${this.MONTH_NAMES[this.miniDate.getMonth()]} ${this.miniDate.getFullYear()}`;
            },

            get mainCalendarDays() {
                return this.generateDaysArray(this.mainDate.getFullYear(), this.mainDate.getMonth());
            },

            get miniCalendarDays() {
                const days = this.generateDaysArray(this.miniDate.getFullYear(), this.miniDate.getMonth());
                const mainStr = this.formatDateString(this.mainDate);
                return days.map(d => ({
                    ...d,
                    isSelected: d.dateString === mainStr
                }));
            },

            get timeGridDays() {
                if (this.currentView === 'day') {
                    return [this.formatDayObject(new Date(this.mainDate), true)];
                } else {
                    // week view (Mon - Sun)
                    const days = [];
                    const curr = new Date(this.mainDate);
                    let dayOfWeek = curr.getDay();
                    let diff = curr.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
                    const monday = new Date(curr.setDate(diff));
                    
                    for (let i = 0; i < 7; i++) {
                        const d = new Date(monday);
                        d.setDate(monday.getDate() + i);
                        days.push(this.formatDayObject(d, d.getMonth() === this.mainDate.getMonth()));
                    }
                    return days;
                }
            },

            getDayName(date) {
                return this.DAY_NAMES[date.getDay()];
            },

            formatHour(hour) {
                if (hour === 0) return '12 AM';
                if (hour === 12) return '12 PM';
                if (hour > 12) return (hour - 12) + ' PM';
                return hour + ' AM';
            },

            get currentTimeTop() {
                const now = new Date();
                const totalMinutes = (now.getHours() * 60) + now.getMinutes();
                return (totalMinutes / 60) * this.hourHeight;
            },

            generateDaysArray(year, month) {
                const days = [];
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                let startDayOfWeek = firstDay.getDay();
                startDayOfWeek = startDayOfWeek === 0 ? 6 : startDayOfWeek - 1; // Mon = 0
                
                const prevMonthLastDay = new Date(year, month, 0).getDate();
                for (let i = startDayOfWeek - 1; i >= 0; i--) {
                    days.push(this.formatDayObject(new Date(year, month - 1, prevMonthLastDay - i), false));
                }
                for (let i = 1; i <= lastDay.getDate(); i++) {
                    days.push(this.formatDayObject(new Date(year, month, i), true));
                }
                const remainingCells = 42 - days.length;
                for (let i = 1; i <= remainingCells; i++) {
                    days.push(this.formatDayObject(new Date(year, month + 1, i), false));
                }
                return days;
            },

            formatDayObject(date, isCurrentMonth) {
                const dateString = this.formatDateString(date);
                const today = new Date();
                const isToday = date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
                return { date, dateString, isCurrentMonth, isToday };
            },
            
            formatDateString(date) {
                return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            },

            async fetchEvents() {
                this.loading = true;
                try {
                    const res = await fetch(`/api/events?_t=${new Date().getTime()}`, {
                        headers: {
                            'Cache-Control': 'no-cache',
                            'Pragma': 'no-cache'
                        }
                    });
                    if (res.ok) {
                        const rawEvents = await res.json();
                        this.allEvents = rawEvents.map(evt => this.processEventFormat(evt));
                    }
                } catch (error) {
                    console.error("Gagal mengambil data kegiatan", error);
                } finally {
                    this.loading = false;
                }
            },

            processEventFormat(evt) {
                let bgColor = '#1E3A8A'; let isPastel = false;
                const category = (evt.extendedProps?.category || '').toLowerCase();
                if (category.includes('pekerjaan') || category.includes('survei')) bgColor = '#14532D'; // dark green
                else if (category.includes('pribadi')) bgColor = '#4C1D95'; // dark purple
                else if (category.includes('pengingat')) bgColor = '#9A3412'; // dark orange
                else { bgColor = '#1E3A8A'; isPastel = false; }
                
                const startStr = evt.start ? evt.start.substring(0, 10) : '';
                const endStr = evt.end ? evt.end.substring(0, 10) : startStr;
                const isMultiDay = startStr !== endStr && endStr !== '';
                
                // Parse time for timegrid
                let startTimeString = ''; let endTimeString = '';
                if (evt.start && evt.start.includes('T')) startTimeString = evt.start.substring(11, 16);
                if (evt.end && evt.end.includes('T')) endTimeString = evt.end.substring(11, 16);

                return {
                    ...evt, bgColor, isPastel, startStr, endStr, isMultiDay,
                    startTimeString, endTimeString,
                    timeString: startTimeString ? `${startTimeString} - ${endTimeString}` : '',
                    displayTitle: (startTimeString && !isMultiDay ? startTimeString + ' ' : '') + evt.title
                };
            },

            get filteredEvents() {
                return this.allEvents.filter(evt => {
                    const cat = (evt.extendedProps?.category || '').toLowerCase();
                    if (this.filters.pekerjaan && (cat.includes('pekerjaan') || cat.includes('survei'))) return true;
                    if (this.filters.pribadi && cat.includes('pribadi')) return true;
                    if (this.filters.pengingat && cat.includes('pengingat')) return true;
                    if (this.filters.saya) return true;
                    return false;
                });
            },

            getEventsForDate(dateString) {
                return this.filteredEvents.filter(evt => {
                    if (!evt.startStr) return false;
                    if (evt.isMultiDay) return dateString >= evt.startStr && dateString <= evt.endStr;
                    return dateString === evt.startStr;
                }).map(evt => ({
                    ...evt, isStart: dateString === evt.startStr, isEnd: dateString === evt.endStr
                }));
            },

            getVisibleEventsForMonth(dateString) {
                const events = this.getEventsForDate(dateString);
                const hasHoliday = !!this.getHolidayForDate(dateString);
                // If there's a holiday, it takes up 1 slot, leaving 1 slot for an event.
                // Otherwise, we have 2 slots for events.
                const maxEvents = hasHoliday ? 0 : 1;
                return events.slice(0, maxEvents);
            },

            getMoreCountForMonth(dateString) {
                const events = this.getEventsForDate(dateString);
                const hasHoliday = !!this.getHolidayForDate(dateString);
                const maxEvents = hasHoliday ? 0 : 1;
                return events.length > maxEvents ? events.length - maxEvents : 0;
            },

            // For Week/Day Views: Separate All Day vs Timed Events & Holidays
            hasAnyAllDayEvents() {
                return this.timeGridDays.some(d => 
                    this.getHolidayForDate(d.dateString) || 
                    this.getAllDayEventsForDate(d.dateString).length > 0
                );
            },

            getAllDayEventsForDate(dateString) {
                return this.getEventsForDate(dateString).filter(evt => evt.isMultiDay || !evt.startTimeString);
            },

            getTimedEventsForDate(dateString) {
                let events = this.getEventsForDate(dateString).filter(evt => !evt.isMultiDay && evt.startTimeString);
                return this.calculateEventLayout(events);
            },

            calculateEventLayout(events) {
                if (events.length === 0) return [];
                
                // 1. Calculate start and end in minutes
                events.forEach(evt => {
                    const [sH, sM] = evt.startTimeString.split(':').map(Number);
                    let [eH, eM] = evt.endTimeString ? evt.endTimeString.split(':').map(Number) : [sH + 1, sM];
                    evt.startMinutes = (sH * 60) + sM;
                    evt.endMinutes = (eH * 60) + eM;
                    if (evt.endMinutes <= evt.startMinutes) evt.endMinutes = evt.startMinutes + 30; // min 30 min
                });

                // 2. Sort events by start time, then duration
                events.sort((a, b) => {
                    if (a.startMinutes !== b.startMinutes) return a.startMinutes - b.startMinutes;
                    return (b.endMinutes - b.startMinutes) - (a.endMinutes - a.startMinutes);
                });

                // 3. Group overlapping events
                let groups = [];
                events.forEach(evt => {
                    if (groups.length === 0) {
                        groups.push([evt]);
                    } else {
                        let lastGroup = groups[groups.length - 1];
                        let maxEnd = Math.max(...lastGroup.map(e => e.endMinutes));
                        if (evt.startMinutes < maxEnd) {
                            lastGroup.push(evt);
                        } else {
                            groups.push([evt]);
                        }
                    }
                });

                // 4. Calculate layout per group
                groups.forEach(group => {
                    let columns = [];
                    group.forEach(evt => {
                        let placed = false;
                        for (let i = 0; i < columns.length; i++) {
                            let col = columns[i];
                            let lastEventInCol = col[col.length - 1];
                            if (evt.startMinutes >= lastEventInCol.endMinutes) {
                                col.push(evt);
                                evt.columnIndex = i;
                                placed = true;
                                break;
                            }
                        }
                        if (!placed) {
                            columns.push([evt]);
                            evt.columnIndex = columns.length - 1;
                        }
                    });

                    const numCols = columns.length;
                    group.forEach(evt => {
                        let colSpan = 1;
                        for (let i = evt.columnIndex + 1; i < numCols; i++) {
                            let overlapInThisCol = columns[i].some(e => 
                                e.startMinutes < evt.endMinutes && e.endMinutes > evt.startMinutes
                            );
                            if (!overlapInThisCol) {
                                colSpan++;
                            } else {
                                break;
                            }
                        }
                        
                        evt.leftPercent = (evt.columnIndex / numCols) * 100;
                        
                        let baseWidth = 100 / numCols;
                        // Cascade overlap: make width 1.7x the base width, so it visually overlaps the next column(s).
                        let widthPercent = baseWidth * colSpan + (baseWidth * 0.7);
                        
                        // Prevent overflowing container
                        if (evt.leftPercent + widthPercent > 100) {
                            widthPercent = 100 - evt.leftPercent;
                        }
                        
                        evt.widthPercent = widthPercent;
                    });
                });

                return events;
            },

            getEventStyles(evt) {
                if (!evt.startTimeString) return 'display: none;';
                
                const topPx = (evt.startMinutes / 60) * this.hourHeight;
                let heightPx = ((evt.endMinutes - evt.startMinutes) / 60) * this.hourHeight;
                if (heightPx < 26) heightPx = 26; // min height

                // OVERLAP STYLE:
                // We use the leftPercent and widthPercent calculated by calculateEventLayout.
                const left = evt.leftPercent || 0;
                const widthPercent = evt.widthPercent || 100;
                
                const width = `calc(${widthPercent}% - 2px)`;
                const leftPos = `calc(${left}% + 1px)`;
                const zIndex = 20 + (evt.columnIndex || 0);

                // Google Calendar style (Solid BG, White Border)
                let bg = evt.bgColor || '#005AA9';
                let textCol = '#ffffff';

                return `
                    top: ${topPx}px; 
                    height: ${heightPx}px; 
                    left: ${leftPos}; 
                    width: ${width}; 
                    background-color: ${bg}; 
                    border: 1px solid #ffffff; 
                    color: ${textCol};
                    z-index: ${zIndex};
                    box-shadow: none !important;
                `;
            },

            goToPrevious() {
                const d = new Date(this.mainDate);
                if (this.currentView === 'year') d.setFullYear(d.getFullYear() - 1);
                else if (this.currentView === 'month') d.setMonth(d.getMonth() - 1);
                else if (this.currentView === 'week') d.setDate(d.getDate() - 7);
                else if (this.currentView === 'day') d.setDate(d.getDate() - 1);
                this.mainDate = d;
                this.syncMiniDate();
            },

            goToNext() {
                const d = new Date(this.mainDate);
                if (this.currentView === 'year') d.setFullYear(d.getFullYear() + 1);
                else if (this.currentView === 'month') d.setMonth(d.getMonth() + 1);
                else if (this.currentView === 'week') d.setDate(d.getDate() + 7);
                else if (this.currentView === 'day') d.setDate(d.getDate() + 1);
                this.mainDate = d;
                this.syncMiniDate();
            },

            goToToday() {
                this.mainDate = new Date();
                this.syncMiniDate();
                this.scrollToCurrentTime();
            },
            
            scrollToCurrentTime() {
                setTimeout(() => {
                    const grid = document.getElementById('timeGridBody');
                    if (grid) {
                        const now = new Date();
                        const currentHour = now.getHours();
                        grid.scrollTop = Math.max(0, (currentHour * this.hourHeight) - 120);
                    }
                }, 100);
            },
            
            changeMiniMonth(offset) {
                this.miniDate = new Date(this.miniDate.getFullYear(), this.miniDate.getMonth() + offset, 1);
            },
            
            selectMiniDate(date) {
                this.mainDate = new Date(date);
                if (this.currentView === 'year') {
                    this.currentView = 'month';
                }
            },
            
            selectDateToDayView(date) {
                this.mainDate = new Date(date);
                this.syncMiniDate();
                this.currentView = 'day';
                this.scrollToCurrentTime();
            },

            syncMiniDate() {
                this.miniDate = new Date(this.mainDate);
            },

            openCreateEventModal(dateString = null, time = '08:00') {
                const targetDate = dateString || this.formatDateString(this.mainDate);
                const nextHour = String(Math.min(23, Number(time.split(':')[0]) + 1)).padStart(2, '0') + ':' + time.split(':')[1];
                
                // Hanya update waktu, pertahankan teks/inputan sebelumnya
                this.createForm.start = `${targetDate}T${time}`;
                this.createForm.end = `${targetDate}T${nextHour}`;
                this.createForm.allDay = false;

                if (!this.createForm.assignees || this.createForm.assignees.length === 0) {
                    this.createForm.assignees = ['{{ $user['id'] ?? '' }}'];
                }
                
                this.showCreateModal = true;
            },

            // --- DRAG SELECTION LOGIC ---
            getTimeFromPointer(event, container) {
                const rect = container.getBoundingClientRect();
                const y = event.clientY - rect.top;
                let totalMinutes = Math.floor(y * (60 / this.hourHeight));
                if (totalMinutes < 0) totalMinutes = 0;
                if (totalMinutes > 1440) totalMinutes = 1440; // Max 24:00
                return Math.floor(totalMinutes / this.SLOT_MINUTES) * this.SLOT_MINUTES;
            },

            startSelection(event, dateString) {
                this.isSelecting = true;
                this.selectedDate = dateString;
                const minutes = this.getTimeFromPointer(event, event.currentTarget);
                this.selectionStart = minutes;
                this.selectionEnd = minutes;
                this.currentDragContainer = event.currentTarget;
                
                // Prevent scrolling while dragging
                document.body.style.userSelect = 'none';
            },

            updateSelection(event) {
                if (!this.isSelecting || !this.currentDragContainer) return;
                this.selectionEnd = this.getTimeFromPointer(event, this.currentDragContainer);
            },

            endSelection(event) {
                if (!this.isSelecting) return;
                
                document.body.style.userSelect = '';
                
                let startMins = Math.min(this.selectionStart, this.selectionEnd);
                let endMins = Math.max(this.selectionStart, this.selectionEnd);
                
                // Jika klik saja (start == end), set durasi default (misal: 60 menit)
                if (startMins === endMins) {
                    endMins += 60;
                    this.selectionEnd = endMins; // Update visualizer agar tampil blok 1 jam
                }
                
                // Buka modal
                const startHour = Math.floor(startMins / 60);
                const startMin = startMins % 60;
                const endHour = Math.floor(endMins / 60);
                const endMin = endMins % 60;

                const startTimeStr = String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0');
                const endTimeStr = String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0');

                // Hanya update waktu, pertahankan teks/inputan sebelumnya
                this.createForm.start = `${this.selectedDate}T${startTimeStr}`;
                this.createForm.end = `${this.selectedDate}T${endTimeStr}`;
                this.createForm.allDay = false;

                if (!this.createForm.assignees || this.createForm.assignees.length === 0) {
                    this.createForm.assignees = ['{{ $user['id'] ?? '' }}'];
                }
                
                this.showCreateModal = true;
                this.currentDragContainer = null;
                // isSelecting TIDAK di-false di sini, agar blok biru tetap muncul saat modal terbuka.
                // Akan di-false otomatis oleh x-watch('showCreateModal') saat modal ditutup.
            },

            getSelectionStyles() {
                const startMins = Math.min(this.selectionStart, this.selectionEnd);
                const endMins = Math.max(this.selectionStart, this.selectionEnd);
                let heightMins = endMins - startMins;
                
                if (heightMins === 0) heightMins = this.SLOT_MINUTES;

                const topPx = (startMins / 60) * this.hourHeight;
                const heightPx = (heightMins / 60) * this.hourHeight;

                return `top: ${topPx}px; height: ${heightPx}px;`;
            },

            
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

                const newStart = `${p.dateString}T${startTimeStr}`;
                const newEnd = `${p.dateString}T${endTimeStr}`;

                evt.start = newStart;
                evt.end = newEnd;
                const updatedEvt = this.processEventFormat(evt);
                const index = this.allEvents.findIndex(e => e.id === evt.id);
                if (index !== -1) {
                    this.allEvents[index] = updatedEvt;
                }

                try {
                    const res = await fetch(`/api/activities/${evt.id}`, {
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
                
                return `top: ${topPx}px; height: ${heightPx}px; left: calc(${left}% + 1px); width: calc(${widthPercent}% - 2px); background-color: ${bg};`;
            },

            formatDragPlaceholderTime() {
                const p = this.dragPlaceholder;
                if (!p) return '';
                const startHour = String(Math.floor(p.startMinutes / 60)).padStart(2, '0');
                const startMin = String(p.startMinutes % 60).padStart(2, '0');
                const endHour = String(Math.floor(p.endMinutes / 60)).padStart(2, '0');
                const endMin = String(p.endMinutes % 60).padStart(2, '0');
                return `${startHour}:${startMin} - ${endHour}:${endMin}`;
            },
,

            // ----------------------------

            async submitCreateEvent() {
                if (!this.createForm.title) {
                    alert('Mohon isi judul kegiatan.');
                    return;
                }
                this.isSubmitting = true;
                try {
                    const res = await fetch('/api/activities', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            title: this.createForm.title,
                            description: this.createForm.description || null,
                            start: this.createForm.start,
                            end: this.createForm.end,
                            allDay: this.createForm.allDay,
                            location: this.createForm.location || null,
                            status: this.createForm.status || 'planned',
                            category: this.createForm.category,
                            assignees: this.createForm.assignees.length ? this.createForm.assignees : ['{{ $user['id'] ?? '' }}']
                        })
                    });

                    if (res.ok) {
                        this.showCreateModal = false;
                        
                        // Clear the form here, only after a successful submit
                        this.createForm = {
                            title: '',
                            category: 'Pekerjaan / Survei',
                            start: '',
                            end: '',
                            allDay: false,
                            location: '',
                            description: '',
                            status: 'planned',
                            assignees: ['{{ $user['id'] ?? '' }}']
                        };

                        await this.fetchEvents();
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menyimpan kegiatan.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan saat menyimpan kegiatan.');
                } finally {
                    this.isSubmitting = false;
                }
            },

            openEventDetail(evt) {
                this.selectedEvent = evt;
                this.showDetailModal = true;
            },

            formatDateRange(start, end) {
                if (!start) return '-';
                const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute:'2-digit' };
                const s = new Date(start).toLocaleString('id-ID', opts);
                if (!end || start === end) return s;
                if (new Date(start).toDateString() === new Date(end).toDateString()) {
                    return `${s} - ${new Date(end).toLocaleString('id-ID', { hour: '2-digit', minute:'2-digit' })}`;
                }
                return `${s} - ${new Date(end).toLocaleString('id-ID', opts)}`;
            },

            async deleteEvent(id) {
                if (!confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) return;
                try {
                    const res = await fetch(`/api/activities/${id}`, { 
                        method: 'DELETE', 
                        headers: { 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        } 
                    });
                    if (res.ok) { 
                        this.showDetailModal = false; 
                        await this.fetchEvents(); 
                    } else { 
                        const err = await res.json(); 
                        alert(err.message || 'Gagal menghapus kegiatan.'); 
                    }
                } catch (e) { 
                    alert('Gagal menghapus kegiatan.'); 
                }
            }
        };
    }
