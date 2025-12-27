<style>
    .calendar-day {
        padding: 0.5rem;
        text-align: center;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }

    .calendar-day.present {
        background-color: #d4edda;
        color: #155724;
    }

    .calendar-day.absent {
        background-color: #f8d7da;
        color: #721c24;
    }

    .calendar-day.late,
    .calendar-day.halfday {
        background-color: #fff3cd;
        color: #856404;
    }
    .calendar .onleave-dot {
        background-color: #b4cc64ff;
    }
    .calendar-day.onleave {
        background-color: #b4cc64ff; /* yellowish background for leave */
        color: #856404;
    }


    .calendar-day:hover {
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
    }

    .text-muted {
        opacity: 0.5;
    }
</style>

<div class="calendar">
    <div class="calendar-header d-flex justify-content-between align-items-center mb-2">
        <span>Calendar View</span>
        <a href="#" class="text-primary fw-semibold text-decoration-none">
            {{ $firstDayOfMonth->format('F, Y') }}
        </a>
    </div>

    <!-- Week Days -->
    <div class="calendar-grid weeks fw-bold d-grid" style="grid-template-columns: repeat(7, 1fr);">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div>{{ $day }}</div>
        @endforeach
    </div>

    <!-- Dates with Highlight Classes -->
    <div class="calendar-grid d-grid" style="grid-template-columns: repeat(7, 1fr); gap: 0.25rem;">
        @foreach ($calendarData as $day)
        
            <div
                class="calendar-day {{ $day['class'] }} {{ $day['is_current_month'] ? '' : 'text-muted' }}"
                title="{{ $day['tooltip'] }}"
            >
                {{ $day['day'] }}
            </div>
        @endforeach
    </div>

    <!-- Legend -->
    <div class="legend border-top mt-3 pt-2 d-flex gap-3">
        <span><span class="legend-dot present-dot"></span> Present</span>
        <span><span class="legend-dot halfday-dot"></span> Late</span>
        <span><span class="legend-dot absent-dot"></span> Absent</span>
        <span><span class="legend-dot onleave-dot"></span> On-Leave</span>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('.calendar-day[title]'))
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el)
        })
    });


    
    document.addEventListener('DOMContentLoaded', function () {
        tippy('.calendar-day[title]', {
            content(reference) {
                return reference.getAttribute('title');
            },
            // Optional settings:
            arrow: true,
            delay: [100, 100],
            placement: 'top',
            animation: 'scale',
        });
    });

</script>
