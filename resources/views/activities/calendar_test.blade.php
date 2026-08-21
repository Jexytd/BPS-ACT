@extends('layouts.app')

@section('title', 'Kalender (Test) — BPS ACT')

@section('content')
<div x-data="googleCalendarApp()" x-init="initCalendar()" class="flex-1 flex flex-col h-full bg-white text-gray-800 font-sans overflow-hidden">
    
    <!-- Header Utama (relative z-50 agar dropdown selalu berada di atas time-grid) -->
    <header class="relative z-50 flex items-center justify-between px-4 py-3 border-b border-gray-200 shrink-0 bg-white shadow-xs">
        <div class="flex items-center gap-4">
            <button @click="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded-full text-gray-600 transition cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            
            <div class="flex items-center gap-4 ml-2 sm:ml-6">
                <button @click="goToToday()" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-50 transition cursor-pointer hidden sm:block">
                    Hari ini
                </button>
                <div class="flex items-center gap-1">
                    <button @click="goToPrevious()" class="p-1.5 hover:bg-gray-100 rounded-full text-gray-600 transition cursor-pointer" title="Sebelumnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="goToNext()" class="p-1.5 hover:bg-gray-100 rounded-full text-gray-600 transition cursor-pointer" title="Berikutnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <h2 class="text-lg sm:text-[22px] font-normal text-gray-700 ml-2" x-text="mainHeaderString"></h2>
            </div>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- View Dropdown -->
            <div class="relative" x-data="{ openViewMenu: false }">
                <button @click="openViewMenu = !openViewMenu" class="border border-gray-300 rounded-md px-3.5 py-1.5 flex items-center gap-2 hover:bg-gray-50 transition cursor-pointer shadow-sm">
                    <span class="text-sm font-medium text-gray-700" x-text="getViewLabel(currentView)"></span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openViewMenu" @click.away="openViewMenu = false" x-transition class="absolute right-0 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-50">
                    <button @click="currentView = 'day'; openViewMenu = false; scrollToCurrentTime()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" :class="currentView === 'day' ? 'bg-blue-50 text-blue-700 font-medium' : ''">Hari</button>
                    <button @click="currentView = 'week'; openViewMenu = false; scrollToCurrentTime()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" :class="currentView === 'week' ? 'bg-blue-50 text-blue-700 font-medium' : ''">Minggu</button>
                    <button @click="currentView = 'month'; openViewMenu = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" :class="currentView === 'month' ? 'bg-blue-50 text-blue-700 font-medium' : ''">Bulan</button>
                    <button @click="currentView = 'year'; openViewMenu = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" :class="currentView === 'year' ? 'bg-blue-50 text-blue-700 font-medium' : ''">Tahun</button>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside x-show="sidebarOpen" x-transition.opacity.duration.200ms class="w-[280px] shrink-0 border-r border-gray-200 overflow-y-auto custom-scrollbar flex flex-col pb-4 bg-white z-10">
            <!-- Tombol Buat (In-Modal Standalone) -->
            <div class="p-4 pl-6">
                <button type="button" @click="openCreateEventModal()" class="inline-flex items-center gap-3 bg-white border border-gray-200 shadow-sm hover:shadow-md hover:bg-gray-50 transition rounded-full px-5 py-3 cursor-pointer">
                    <svg class="w-6 h-6" viewBox="0 0 36 36"><path fill="#34A853" d="M16 16v14h4V20z"></path><path fill="#4285F4" d="M30 16H20l-4 4h14z"></path><path fill="#FBBC05" d="M6 16v4h10l4-4z"></path><path fill="#EA4335" d="M20 16V2h-4v14z"></path><path fill="none" d="M0 0h36v36H0z"></path></svg>
                    <span class="text-[15px] font-medium text-gray-700 tracking-wide">Buat</span>
                </button>
            </div>

            <!-- Mini Calendar -->
            <div class="px-6 py-2 hidden sm:block">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[13px] font-medium text-gray-800" x-text="miniMonthYearString"></span>
                    <div class="flex gap-1">
                        <button @click="changeMiniMonth(-1)" class="p-1 hover:bg-gray-100 rounded-full text-gray-600 cursor-pointer"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                        <button @click="changeMiniMonth(1)" class="p-1 hover:bg-gray-100 rounded-full text-gray-600 cursor-pointer"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                    </div>
                </div>
                <!-- Mini Grid -->
                <div>
                    <div class="grid grid-cols-7 mb-1">
                        <template x-for="day in ['S', 'S', 'R', 'K', 'J', 'S', 'M']">
                            <div class="text-[10px] font-medium text-gray-500 text-center" x-text="day"></div>
                        </template>
                    </div>
                    <div class="grid grid-cols-7 gap-y-0.5">
                        <template x-for="mDay in miniCalendarDays" :key="mDay.dateString">
                            <div @click="selectMiniDate(mDay.date)" 
                                 class="w-7 h-7 flex items-center justify-center text-[10px] rounded-full mx-auto cursor-pointer transition relative"
                                 :class="{
                                     'text-blue-600 bg-blue-100 hover:bg-blue-200 font-semibold': mDay.isToday && !mDay.isSelected,
                                     'bg-blue-600 text-white shadow-sm': mDay.isSelected,
                                     'text-gray-700 hover:bg-gray-100': mDay.isCurrentMonth && !mDay.isToday && !mDay.isSelected,
                                     'text-gray-400 hover:bg-gray-50': !mDay.isCurrentMonth && !mDay.isSelected
                                 }">
                                <span x-text="mDay.date.getDate()"></span>
                                <template x-if="mDay.isCurrentMonth && getHolidayForDate(mDay.dateString)">
                                    <div class="absolute -bottom-0.5 w-1 h-1 rounded-full bg-emerald-600"></div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="mt-4">
                <div class="px-6 py-2 flex items-center justify-between group cursor-pointer" @click="showMyCals = !showMyCals">
                    <span class="text-[13px] font-medium text-gray-800">Kalender saya</span>
                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="showMyCals ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div x-show="showMyCals" class="px-4 pb-2 space-y-1">
                    <template x-for="(val, key) in filters" :key="key">
                        <label class="flex items-center gap-3 px-2 py-1.5 hover:bg-gray-100 rounded cursor-pointer transition">
                            <input type="checkbox" x-model="filters[key]" class="w-4 h-4 rounded" 
                                   :class="{
                                        'text-blue-600 focus:ring-blue-500': key === 'saya',
                                        'text-green-600 focus:ring-green-500': key === 'pekerjaan',
                                        'text-purple-600 focus:ring-purple-500': key === 'pribadi',
                                        'text-yellow-500 focus:ring-yellow-400': key === 'pengingat',
                                        'text-emerald-700 focus:ring-emerald-600': key === 'libur'
                                   }">
                            <span class="text-[13px] text-gray-700 capitalize" x-text="key === 'libur' ? 'Hari Libur Nasional' : key"></span>
                        </label>
                    </template>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-white relative"
              @pointermove.window="updateSelection($event); updateEventDrag($event);"
              @pointerup.window="endSelection($event); endEventDrag($event);">
            <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-30" style="display: none;">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <!-- ================= VIEW: MONTH ================= -->
            <template x-if="currentView === 'month'">
                <div class="flex-1 flex flex-col overflow-hidden">
                    <div class="flex-1 grid grid-cols-7 auto-rows-fr bg-white overflow-y-auto custom-scrollbar border-l border-t border-gray-200">
                        <template x-for="(dayObj, index) in mainCalendarDays" :key="dayObj.dateString">
                            <div @click="openCreateEventModal(dayObj.dateString, '08:00')" 
                                 class="bg-white min-h-[160px] flex flex-col relative group cursor-pointer overflow-hidden border-r border-b border-gray-200 hover:bg-gray-50/50 transition">
                                <div class="flex flex-col items-center pt-1 pb-0.5">
                                    <template x-if="index < 7">
                                        <span class="text-[11px] font-medium text-gray-500 uppercase tracking-wider mb-1" x-text="DAY_NAMES[dayObj.date.getDay()]"></span>
                                    </template>
                                    <div class="flex items-center justify-center rounded-full mb-2 text-xs font-medium h-4 min-w-[28px]"
                                         :class="{
                                             'px-2': dayObj.date.getDate() === 1,
                                             'bg-blue-600 px-2 text-white': dayObj.isToday, 
                                             'text-red-600 font-semibold': !dayObj.isToday && dayObj.isCurrentMonth && getHolidayForDate(dayObj.dateString),
                                             'text-gray-700': dayObj.isCurrentMonth && !dayObj.isToday && !getHolidayForDate(dayObj.dateString), 
                                             'text-gray-400': !dayObj.isCurrentMonth && !dayObj.isToday, 
                                             'group-hover:bg-gray-200': !dayObj.isToday && dayObj.date.getDate() === 1,
                                             'group-hover:bg-gray-100': !dayObj.isToday && dayObj.date.getDate() !== 1
                                         }">
                                        <span x-text="dayObj.date.getDate() === 1 ? MONTH_NAMES[dayObj.date.getMonth()].substring(0,3) + ' ' + dayObj.date.getDate() : dayObj.date.getDate()"></span>
                                    </div>
                                </div>
                                <div class="flex-1 overflow-hidden px-1 space-y-0.5 pb-0.5">
                                    <!-- Libur Nasional Badge -->
                                    <template x-if="getHolidayForDate(dayObj.dateString)">
                                        <div class="px-1.5 py-0.5 text-[11px] font-semibold rounded-sm bg-[#0b8043] text-white truncate mx-0.5 flex items-center gap-1 shadow-2xs"
                                             :title="getHolidayForDate(dayObj.dateString)">
                                            <span class="truncate" x-text="getHolidayForDate(dayObj.dateString)"></span>
                                        </div>
                                    </template>

                                    <!-- Event 1 (Jika tidak ada libur) -->
                                    <template x-for="evt in getVisibleEventsForMonth(dayObj.dateString)" :key="evt.id">
                                        <div @click.stop="openEventDetail(evt)" 
                                             class="px-1.5 py-0.5 text-[11px] font-medium truncate cursor-pointer hover:opacity-90 transition-opacity flex items-center gap-1"
                                             :class="{'rounded-sm mx-0.5': !evt.isMultiDay, 'rounded-l-sm ml-0.5 border-r-0': evt.isMultiDay && evt.isStart, 'rounded-r-sm mr-0.5 border-l-0': evt.isMultiDay && evt.isEnd, 'rounded-none mx-0 border-x-0': evt.isMultiDay && !evt.isStart && !evt.isEnd, 'text-white': !evt.isPastel, 'text-gray-800': evt.isPastel}"
                                             :style="'background-color: ' + evt.bgColor + '; border: 1px solid ' + (evt.isPastel ? 'transparent' : 'rgba(0,0,0,0.1)')">
                                             <span class="truncate" x-text="evt.displayTitle"></span>
                                        </div>
                                    </template>
                                    
                                    <!-- +X more / lagi -->
                                    <template x-if="getMoreCountForMonth(dayObj.dateString) > 0">
                                        <div @click.stop="openDayModal(dayObj, $event)" 
                                             class="px-1.5 py-[1px] text-[11px] font-semibold text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded cursor-pointer mx-0.5 transition-colors flex items-center select-none">
                                             <span x-text="getMoreCountForMonth(dayObj.dateString) + ' more'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ================= VIEW: YEAR ================= -->
            <template x-if="currentView === 'year'">
                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-white">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        <template x-for="monthIndex in [0,1,2,3,4,5,6,7,8,9,10,11]" :key="monthIndex">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800 mb-2 ml-2" x-text="MONTH_NAMES[monthIndex]"></h3>
                                <div class="grid grid-cols-7 mb-1">
                                    <template x-for="day in ['S', 'S', 'R', 'K', 'J', 'S', 'M']">
                                        <div class="text-[10px] font-medium text-gray-500 text-center" x-text="day"></div>
                                    </template>
                                </div>
                                <div class="grid grid-cols-7 gap-y-0.5">
                                    <template x-for="mDay in generateDaysArray(mainDate.getFullYear(), monthIndex)" :key="mDay.dateString">
                                        <div @click="selectDateToDayView(mDay.date)" 
                                             class="w-7 h-7 flex items-center justify-center text-[11px] rounded-full mx-auto cursor-pointer transition relative"
                                             :class="{
                                                 'text-white bg-blue-600 shadow-sm font-medium': mDay.isToday, 
                                                 'text-red-600 font-semibold hover:bg-red-50': !mDay.isToday && mDay.isCurrentMonth && getHolidayForDate(mDay.dateString),
                                                 'text-gray-700 hover:bg-gray-100': mDay.isCurrentMonth && !mDay.isToday && !getHolidayForDate(mDay.dateString), 
                                                 'text-gray-300': !mDay.isCurrentMonth
                                             }">
                                            <span x-text="mDay.date.getDate()"></span>
                                            <template x-if="mDay.isCurrentMonth && (getEventsForDate(mDay.dateString).length > 0 || getHolidayForDate(mDay.dateString))">
                                                <div class="absolute bottom-0.5 w-1 h-1 rounded-full"
                                                     :class="getHolidayForDate(mDay.dateString) ? 'bg-emerald-600' : 'bg-blue-500'"></div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ================= VIEW: WEEK & DAY (STANDARDIZED TIME GRID) ================= -->
            <template x-if="currentView === 'week' || currentView === 'day'">
                <div class="flex-1 flex flex-col overflow-hidden bg-white">
                    
                    <!-- Pinned Header Area (Day & Date + GMT+07 + All-Day Row) -->
                    <div class="flex flex-col border-b border-gray-200 shrink-0 bg-white z-20 pr-[8px]">
                        <!-- Top Part: Day Column Names & Numbers -->
                        <div class="flex bg-white">
                            <!-- Left Spacer (64px) -->
                            <div class="w-16 shrink-0 bg-white"></div>
                            
                            <!-- Day Column Headers -->
                            <div class="flex-1 grid" :class="currentView === 'week' ? 'grid-cols-7' : 'grid-cols-1'">
                                <template x-for="dayObj in timeGridDays" :key="dayObj.dateString">
                                    <div class="pt-3 pb-1.5 flex flex-col select-none"
                                         :class="currentView === 'day' ? 'items-start pl-6 justify-center' : 'items-center justify-center'">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-[11px] font-semibold uppercase tracking-wider mb-1 text-gray-500"
                                                  :class="{'text-blue-600 font-bold': dayObj.isToday}"
                                                  x-text="getDayName(dayObj.date)"></span>
                                            <div class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-normal transition-all"
                                                 @click="selectDateToDayView(dayObj.date)"
                                                 :class="{
                                                     'bg-blue-600 text-white font-medium shadow-sm hover:bg-blue-700 cursor-pointer': dayObj.isToday, 
                                                     'text-gray-800 hover:bg-gray-100 cursor-pointer': !dayObj.isToday
                                                 }">
                                                <span x-text="dayObj.date.getDate()"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Bottom Part: GMT Bar & All-Day / Holidays Row -->
                        <div class="flex bg-white min-h-[30px] items-center pb-1.5 pt-0.5">
                            <!-- Left: GMT+07 Label -->
                            <div class="w-16 shrink-0 flex items-center justify-end pr-2.5 select-none">
                                <span class="text-[11px] font-medium text-gray-600 tracking-tight">GMT+07</span>
                            </div>
                            
                            <!-- Right: All-Day / Holiday Badges for each column -->
                            <div class="flex-1 grid" :class="currentView === 'week' ? 'grid-cols-7' : 'grid-cols-1'">
                                <template x-for="dayObj in timeGridDays" :key="dayObj.dateString">
                                    <div class="px-1.5 space-y-1 min-h-[24px] flex flex-col justify-center">
                                        <!-- Libur Nasional Banner (Google Green) -->
                                        <template x-if="getHolidayForDate(dayObj.dateString)">
                                            <div class="px-2.5 py-0.5 bg-[#0b8043] text-white text-[11px] font-medium rounded-full shadow-2xs flex items-center gap-1.5 select-none w-full truncate cursor-default"
                                                 :title="getHolidayForDate(dayObj.dateString)">
                                                <span class="truncate" x-text="getHolidayForDate(dayObj.dateString)"></span>
                                            </div>
                                        </template>

                                        <!-- Custom All-Day Events -->
                                        <template x-for="evt in getAllDayEventsForDate(dayObj.dateString)" :key="evt.id">
                                            <div @click.stop="openEventDetail(evt)" 
                                                 class="px-2 py-0.5 text-[11px] font-medium truncate cursor-pointer hover:shadow-xs transition-shadow rounded-full flex items-center gap-1.5"
                                                 :class="{'text-white': !evt.isPastel, 'text-gray-800': evt.isPastel}"
                                                 :style="'background-color: ' + evt.bgColor + '; border: 1px solid ' + (evt.isPastel ? 'rgba(0,0,0,0.06)' : 'rgba(0,0,0,0.12)')">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="!evt.isPastel ? 'bg-white/80' : 'bg-gray-700/60'"></span>
                                                <span class="truncate" x-text="evt.title"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Time Grid Body (24 Jam: 00:00 - 23:59, 1 jam = 60px -> Total 1440px) -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar relative flex bg-white" id="timeGridBody">
                        
                        <!-- Left: Time Axis (64px) -->
                        <div class="w-16 shrink-0 relative bg-white border-r border-gray-200 select-none pointer-events-none" style="height: 1440px;">
                            <template x-for="i in 24" :key="i">
                                <div class="h-[60px] relative border-b border-transparent">
                                    <!-- Label jam diposisikan persis di atas garis pembagi -->
                                    <span class="absolute -top-2.5 right-2 text-[11px] font-normal text-gray-500" 
                                          x-show="i > 1" 
                                          x-text="formatHour(i - 1)"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Right: Day Columns Container (Canvas Kalender) -->
                        <div class="flex-1 relative bg-white grid min-w-0" :class="currentView === 'week' ? 'grid-cols-7' : 'grid-cols-1'">
                            
                            <!-- Day Columns (1 kolom di Mode Hari, 7 kolom di Mode Minggu) -->
                            <template x-for="dayObj in timeGridDays" :key="dayObj.dateString">
                                <div class="relative border-r border-gray-200 last:border-r-0 min-w-0 cursor-pointer bg-white" 
                                     :data-date="dayObj.dateString"
                                     @pointerdown.prevent="startSelection($event, dayObj.dateString)">
                                    
                                    <!-- 24 Hour Grid Slots (Masing-masing 60px dengan border jam dan garis 30 menit) -->
                                    <template x-for="i in 24" :key="i">
                                        <div class="h-[60px] border-b border-gray-200 relative select-none">
                                            <!-- Garis Putus-Putus 30 Menit -->
                                            <div class="absolute top-[30px] left-0 right-0 border-b border-gray-100 border-dashed pointer-events-none"></div>
                                        </div>
                                    </template>

                                    <!-- Overlaid Event & Indicator Layer -->
                                    <div class="absolute inset-0 pointer-events-none">
                                        <!-- Red Current Time Indicator (Hanya muncul jika kolom adalah hari ini) -->
                                        <template x-if="dayObj.isToday">
                                            <div class="absolute left-0 right-0 z-30 flex items-center"
                                                 :style="'top: ' + currentTimeTop + 'px;'">
                                                <div class="w-3 h-3 rounded-full bg-red-500 -ml-1.5 shadow-sm"></div>
                                                <div class="flex-1 h-[2px] bg-red-500"></div>
                                            </div>
                                        </template>

                                        <!-- Selection Visualizer -->
                                        <template x-if="isSelecting && selectedDate === dayObj.dateString">
                                            <div class="absolute left-1 right-1 bg-blue-500/20 border border-blue-500/50 rounded pointer-events-none z-40 transition-all duration-75"
                                                 :style="getSelectionStyles()">
                                            </div>
                                        </template>

                                        
                                        
                                        
                                        <!-- Event Cards di Day Column -->
                                        <template x-for="evt in getTimedEventsForDate(dayObj.dateString)" :key="evt.id">
                                            <div @click.stop="openEventDetail(evt)" @pointerdown.stop="startEventDrag($event, evt, dayObj.dateString)"
         class="absolute rounded-md p-1.5 cursor-pointer hover:shadow-md transition-all flex flex-col overflow-hidden pointer-events-auto" style="touch-action: none;"
                                                 :class="{'text-white shadow-sm': !evt.isPastel, 'text-gray-800': evt.isPastel, 'ring-2 ring-blue-500 scale-[1.02] opacity-90 z-50 pointer-events-none': isDraggingCard && draggedEvent?.id === evt.id}"
                                                 :style="getEventStyles(evt)">
                                                <div class="font-semibold text-xs leading-tight truncate" x-text="evt.title"></div>
                                                <div class="text-[11px] opacity-90 leading-tight truncate mt-0.5 font-medium" x-text="evt.timeString"></div>
                                                <template x-if="evt.extendedProps?.location">
                                                    <div class="text-[10px] opacity-85 leading-tight truncate mt-1 flex items-center gap-1">
                                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                        <span class="truncate" x-text="evt.extendedProps.location"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        
</main>
    </div>

    <!-- ================= MODAL BUAT KEGIATAN BARU (STANDALONE) ================= -->
    <div x-show="showCreateModal" class="relative z-[100]" aria-labelledby="create-modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showCreateModal" x-transition @click.away="showCreateModal = false" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full border border-gray-100">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Buat Kegiatan Baru</h3>
                        </div>
                        <button type="button" @click="showCreateModal = false" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-200 rounded-full transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form @submit.prevent="submitCreateEvent()" class="p-6 space-y-4">
                        <!-- Judul Kegiatan -->
                        <div>
                            <input type="text" x-model="createForm.title" required placeholder="Tambahkan judul kegiatan..." 
                                   class="w-full text-lg font-medium text-gray-900 placeholder-gray-400 border-b border-gray-300 pb-2 focus:border-blue-600 focus:outline-none transition">
                        </div>

                        <!-- Kategori Kegiatan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Kategori</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="createForm.category = 'Pekerjaan / Survei'" 
                                        :class="createForm.category === 'Pekerjaan / Survei' ? 'bg-green-100 text-green-800 border-green-300 font-medium' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                                        class="px-3 py-1.5 rounded-full text-xs border transition flex items-center gap-1.5 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Pekerjaan / Survei
                                </button>
                                <button type="button" @click="createForm.category = 'Pribadi'" 
                                        :class="createForm.category === 'Pribadi' ? 'bg-purple-100 text-purple-800 border-purple-300 font-medium' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                                        class="px-3 py-1.5 rounded-full text-xs border transition flex items-center gap-1.5 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> Pribadi
                                </button>
                                <button type="button" @click="createForm.category = 'Pengingat'" 
                                        :class="createForm.category === 'Pengingat' ? 'bg-yellow-100 text-yellow-800 border-yellow-300 font-medium' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                                        class="px-3 py-1.5 rounded-full text-xs border transition flex items-center gap-1.5 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Pengingat
                                </button>
                            </div>
                        </div>

                        <!-- Waktu & Tanggal -->
                        <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 font-medium mb-1">Mulai</label>
                                    <input type="datetime-local" x-model="createForm.start" required 
                                           class="w-full text-xs bg-white border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 font-medium mb-1">Selesai</label>
                                    <input type="datetime-local" x-model="createForm.end" required 
                                           class="w-full text-xs bg-white border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                            <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer pt-1">
                                <input type="checkbox" x-model="createForm.allDay" class="w-3.5 h-3.5 text-blue-600 rounded">
                                <span>Sepanjang hari</span>
                            </label>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Lokasi</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                </span>
                                <input type="text" x-model="createForm.location" placeholder="Ruang Rapat, Zoom, atau Lapangan..." 
                                       class="w-full pl-9 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Deskripsi</label>
                            <textarea x-model="createForm.description" rows="2" placeholder="Catatan atau rincian kegiatan..." 
                                      class="w-full p-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:outline-none"></textarea>
                        </div>

                        <!-- Penerima Tugas (Assignees) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Petugas Ditugaskan</label>
                            <div class="max-h-28 overflow-y-auto custom-scrollbar border border-gray-200 rounded-lg p-2 bg-gray-50/50 space-y-1">
                                @if(isset($users) && count($users) > 0)
                                    @foreach($users as $u)
                                        <label class="flex items-center gap-2.5 px-2 py-1 hover:bg-gray-100 rounded text-xs text-gray-700 cursor-pointer">
                                            <input type="checkbox" value="{{ $u['id'] }}" x-model="createForm.assignees" class="w-3.5 h-3.5 text-blue-600 rounded">
                                            <span>{{ $u['name'] }}</span>
                                            <span class="text-[10px] text-gray-400">({{ $u['role'] ?? 'Staff' }})</span>
                                        </label>
                                    @endforeach
                                @else
                                    <label class="flex items-center gap-2 text-xs text-gray-600">
                                        <input type="checkbox" value="{{ $user['id'] ?? '' }}" checked disabled class="w-3.5 h-3.5 text-blue-600 rounded">
                                        <span>{{ $user['name'] ?? 'Saya' }}</span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                                <span x-show="!isSubmitting">Simpan Kegiatan</span>
                                <span x-show="isSubmitting" style="display: none;" class="flex items-center gap-1">
                                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= DAY EVENTS MODAL (Month View "More" Popover) ================= -->
    <div x-show="showDayModal" class="relative z-[100]" aria-labelledby="day-modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="showDayModal" @click="showDayModal = false" class="fixed inset-0 bg-transparent"></div>
        <div x-show="showDayModal" x-transition 
             id="dayEventsModal"
             class="fixed z-10 overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-opacity sm:w-64 w-11/12 border border-gray-200"
             style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    
                    <button type="button" @click="showDayModal = false" class="absolute top-2 right-2 p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200/50 rounded-full transition cursor-pointer z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="flex flex-col px-4 pt-5 pb-2">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-[10px] font-medium text-gray-500 uppercase tracking-widest" x-text="selectedDayObj ? getDayName(selectedDayObj.date) : ''"></span>
                            <h3 class="text-3xl font-normal text-gray-800 leading-none mt-1" x-text="selectedDayObj ? selectedDayObj.date.getDate() : ''"></h3>
                        </div>
                    </div>

                    <div class="px-3 pb-5 space-y-1 max-h-72 overflow-y-auto custom-scrollbar">
                        <!-- Holidays -->
                        <template x-if="selectedDayObj && getHolidayForDate(selectedDayObj.dateString)">
                            <div class="px-2 py-1 text-[11px] font-medium rounded hover:bg-black/5 cursor-pointer flex items-center gap-2 mb-1 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-[#0b8043]"></span>
                                <span class="flex-1 min-w-0 text-gray-800" 
                                      x-text="(getHolidayForDate(selectedDayObj.dateString) || '').length > 30 ? (getHolidayForDate(selectedDayObj.dateString) || '').substring(0, 30) + '...' : getHolidayForDate(selectedDayObj.dateString)"
                                      :title="getHolidayForDate(selectedDayObj.dateString)"></span>
                            </div>
                        </template>

                        <!-- All Events for the selected day -->
                        <template x-if="selectedDayObj">
                            <template x-for="evt in getEventsForDate(selectedDayObj.dateString)" :key="evt.id">
                                <div @click="showDayModal = false; openEventDetail(evt)" 
                                     class="px-2 py-1 text-[11px] font-medium cursor-pointer hover:bg-black/5 rounded flex items-center gap-2 transition-colors">
                                     <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="'background-color: ' + evt.bgColor"></span>
                                     <span class="text-gray-700 shrink-0" x-text="evt.timeString || 'All Day'"></span>
                                     <span class="flex-1 min-w-0 text-gray-900 font-semibold" 
                                           x-text="(evt.title || '').length > 30 ? (evt.title || '').substring(0, 30) + '...' : evt.title"
                                           :title="evt.title"></span>
                                </div>
                            </template>
                        </template>
                        <template x-if="selectedDayObj && getEventsForDate(selectedDayObj.dateString).length === 0 && !getHolidayForDate(selectedDayObj.dateString)">
                            <div class="text-center text-gray-500 text-xs py-4">Tidak ada kegiatan</div>
                        </template>
                    </div>
                </div>
    </div>

    <!-- ================= DETAIL EVENT MODAL ================= -->
    <div x-show="showDetailModal" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="showDetailModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showDetailModal" x-transition @click.away="showDetailModal = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md w-full border border-gray-100">
                    <div class="flex items-center justify-end px-4 py-2 bg-gray-50 border-b border-gray-100 gap-2">
                        <template x-if="(selectedEvent?.extendedProps?.created_by === '{{ $user['id'] ?? '' }}') || ['admin', 'lead'].includes('{{ $user['role'] ?? '' }}')">
                            <div class="flex gap-1">
                                <button type="button" @click="deleteEvent(selectedEvent?.id)" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-full transition cursor-pointer" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <button @click="showDetailModal = false" class="p-1.5 text-gray-500 hover:text-gray-800 hover:bg-gray-200 rounded-full transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="flex gap-4">
                            <div class="w-4 h-4 rounded mt-1 shrink-0" :style="'background-color: ' + (selectedEvent?.bgColor || '#005AA9')"></div>
                            <div class="flex-1 space-y-4">
                                <div>
                                    <h3 class="text-xl font-medium text-gray-900 leading-snug" x-text="selectedEvent?.title"></h3>
                                    <p class="text-sm text-gray-600 mt-1" x-text="formatDateRange(selectedEvent?.start, selectedEvent?.end)"></p>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-700">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span x-text="(selectedEvent?.extendedProps?.assignees_rich?.length || 0) + ' orang ditugaskan'"></span>
                                </div>
                                <div class="flex items-start gap-3 text-sm text-gray-700" x-show="selectedEvent?.extendedProps?.location">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span x-text="selectedEvent?.extendedProps?.location"></span>
                                </div>
                                <div class="flex items-start gap-3 text-sm text-gray-700" x-show="selectedEvent?.extendedProps?.description">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    <div class="whitespace-pre-wrap break-words" x-text="selectedEvent?.extendedProps?.description"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
            justDragged: false,
            dragUpdateCount: 0,
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

                
                event.currentTarget.setPointerCapture(event.pointerId);
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'grabbing';
                this.dragUpdateCount = 0;

            },

            updateEventDrag(event) {
                if (!this.isDraggingCard || !this.draggedEvent) return;

                
                this.dragUpdateCount++;
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

                this.draggedEvent.zIndex = 50; // bring to front while dragging
            },

            async endEventDrag(event) {
                if (!this.isDraggingCard || !this.draggedEvent) return;

                
                if (event && event.currentTarget && event.currentTarget.releasePointerCapture) {
                    try { event.currentTarget.releasePointerCapture(event.pointerId); } catch(e){}
                }
                document.body.style.userSelect = '';
                document.body.style.cursor = '';


                const evt = this.draggedEvent;
                const orig = this.originalDragEventState;
                
                this.isDraggingCard = false;
                this.draggedEvent = null;

                // Clean up drag styles
                delete evt.zIndex;

                // Did it change?
                if (orig.startStr !== evt.startStr || orig.startMinutes !== evt.startMinutes) {
                    this.justDragged = true;
                    setTimeout(() => this.justDragged = false, 200);
                }
                if (orig.startStr === evt.startStr && orig.startMinutes === evt.startMinutes) {
                    return; // No change
                }

                const startHour = Math.floor(evt.startMinutes / 60);
                const startMin = evt.startMinutes % 60;
                const endHour = Math.floor(evt.endMinutes / 60);
                const endMin = evt.endMinutes % 60;

                const startTimeStr = String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0');
                const endTimeStr = String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0');

                const newStart = `${evt.startStr}T${startTimeStr}`;
                const newEnd = `${evt.startStr}T${endTimeStr}`;

                evt.start = newStart;
                evt.end = newEnd;

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
                        // Refresh to sync layout formatting with others
                        this.fetchEvents();
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan saat memindahkan kegiatan.');
                    this.fetchEvents(); 
                }
            },

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
                if (this.justDragged) return;
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
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.4); border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.7); }
</style>
@endsection
