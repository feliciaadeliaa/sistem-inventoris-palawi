import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import listPlugin from "@fullcalendar/list";
import timeGridPlugin from "@fullcalendar/timegrid";

// Urutan penting: cek yang lebih spesifik dulu biar gak ketuker antar status
const STATUS_COLORS = [
  { match: /ditolak/i, cssVar: "--status-ditolak", fallback: "#F04438" },
  { match: /disetujui/i, cssVar: "--status-disetujui", fallback: "#028C4F" },
  { match: /diproses/i, cssVar: "--status-diproses", fallback: "#004E68" },
  { match: /diajukan/i, cssVar: "--status-diajukan", fallback: "#FB6514" },
  { match: /dikembalikan/i, cssVar: "--status-dikembalikan", fallback: "#3DA597" },
];

function readCssVar(varName, fallback) {
  const value = getComputedStyle(document.documentElement)
    .getPropertyValue(varName)
    .trim();
  return value || fallback;
}

function resolveEventColor(title = "") {
  const found = STATUS_COLORS.find((s) => s.match.test(title));
  if (!found) return "#00838C"; // fallback brand-500
  return readCssVar(found.cssVar, found.fallback);
}

export function calendarInit() {
  const calendarEl = document.querySelector("#calendar");
  const legendEl = document.querySelector("#calendar-legend");

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
      eventDataTransform: function (eventData) {
        const color = resolveEventColor(eventData.title);
        return {
          ...eventData,
          backgroundColor: color,
          borderColor: color,
          textColor: "#ffffff",
        };
      },
      datesSet: function (info) {
        if (legendEl) {
          legendEl.style.display =
            info.view.type === "dayGridMonth" ? "flex" : "none";
        }
      },
    });

    calendar.render();
  }
}

export default calendarInit;