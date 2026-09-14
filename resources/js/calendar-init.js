import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import listPlugin from "@fullcalendar/list";
import timeGridPlugin from "@fullcalendar/timegrid";

export function calendarInit() {
  const calendarEl = document.querySelector("#calendar");

  if (calendarEl) {
    const calendar = new Calendar(calendarEl, {
      plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
      initialView: "dayGridMonth",
      headerToolbar: {
        left: "prev,next",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek",
      },
      events: "/calendar/events",
      eventDisplay: "block",
      displayEventTime: false,
    });

    calendar.render();
  }
}

export default calendarInit;