let pullData = async () => {
    const response = await fetch('schedule.php');
    const json = await response.json();
    return json;
}

let showInfoPopup = (info) => {
    vex.dialog.alert({
        unsafeMessage: `<h2>${info.event.title}</h2><h3>Presented by ${info.event.extendedProps.showPresenter}</h3><p>Description</p>`
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    vex.defaultOptions.className = 'vex-theme-plain';

    pullData().then((data) => {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'timeGrid' ],
            defaultView: 'timeGridWeek',
            columnHeaderFormat: { weekday: 'long' },
            header: { left: '', center: '', right: '' },
            firstDay: 1,
            slotDuration: '01:00:00',
            nowIndicator: true,
            allDaySlot: false,
            events: data,
            eventClick: showInfoPopup,
            eventColor: window.getComputedStyle(document.documentElement).getPropertyValue('--forge-blue')
        });

        calendar.render();
    });
});