import { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { getPlanning } from "../api/planningApi";
import "../styles/planning.css";

const DAYS = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

const formatDateLabel = (value) => {
  if (!value) return "Sans date";
  return new Date(value).toLocaleDateString("fr-FR", {
    weekday: "long",
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
};

const formatTimeRange = (start, end) => {
  if (!start && !end) return "Pas de créneau";

  const startLabel = start
    ? new Date(start).toLocaleString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      })
    : "?";

  const endLabel = end
    ? new Date(end).toLocaleString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      })
    : "?";

  return `${startLabel} → ${endLabel}`;
};

const statusBadgeClass = (status) => {
  switch (status) {
    case "terminee":
      return "badge badge--green";
    case "en_cours":
      return "badge badge--blue";
    case "bloquee":
    case "annulee":
      return "badge badge--red";
    default:
      return "badge badge--orange";
  }
};

const priorityBadgeClass = (priority) => {
  switch (priority) {
    case "critique":
    case "haute":
      return "badge badge--red";
    case "moyenne":
      return "badge badge--orange";
    case "basse":
      return "badge badge--green";
    default:
      return "badge badge--blue";
  }
};

const getMonthStart = (date) => new Date(date.getFullYear(), date.getMonth(), 1);
const getMonthEnd = (date) => new Date(date.getFullYear(), date.getMonth() + 1, 0);

const buildCalendarDays = (date) => {
  const start = getMonthStart(date);
  const end = getMonthEnd(date);

  const startWeekday = (start.getDay() + 6) % 7;
  const gridStart = new Date(start);
  gridStart.setDate(start.getDate() - startWeekday);

  const endWeekday = (end.getDay() + 6) % 7;
  const gridEnd = new Date(end);
  gridEnd.setDate(end.getDate() + (6 - endWeekday));

  const days = [];
  const cursor = new Date(gridStart);

  while (cursor <= gridEnd) {
    days.push(new Date(cursor));
    cursor.setDate(cursor.getDate() + 1);
  }

  return days;
};

const occursOnDay = (item, day) => {
  const dayStart = new Date(day);
  dayStart.setHours(0, 0, 0, 0);

  const dayEnd = new Date(day);
  dayEnd.setHours(23, 59, 59, 999);

  const start = item.dateDebut ? new Date(item.dateDebut) : null;
  const end = item.dateFin ? new Date(item.dateFin) : start;

  if (!start) return false;
  return start <= dayEnd && (end ?? start) >= dayStart;
};

export default function PlanningPage() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState("timeline");
  const [currentMonth, setCurrentMonth] = useState(new Date());
  const [filters, setFilters] = useState({
    type: "campagne",
    statut: "",
    priorite: "",
    search: "",
    from: "",
    to: "",
  });

  const fetchPlanning = async () => {
    setLoading(true);
    try {    
      const data = await getPlanning({
        ...filters,
        type: "campagne",
        assigned: 1,
      });
      setItems(data);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPlanning();
  }, [filters.type, filters.statut, filters.priorite, filters.search, filters.from, filters.to]);

  const summary = useMemo(() => {
    const campagnes = items.filter((item) => item.type === "campagne").length;
    const enCours = items.filter((item) => item.statut === "en_cours").length;
    const critiques = items.filter((item) => item.priorite === "critique").length;

    return { campagnes, enCours, critiques };
  }, [items]);

  const groupedTimeline = useMemo(() => {
    return items.reduce((acc, item) => {
      const key = item.dateDebut ? new Date(item.dateDebut).toISOString().slice(0, 10) : "sans-date";
      acc[key] = acc[key] || [];
      acc[key].push(item);
      return acc;
    }, {});
  }, [items]);

  const calendarDays = useMemo(() => buildCalendarDays(currentMonth), [currentMonth]);

  return (
    <div className="planning-page">
      <div className="page__header">
        <div>
          <h1 className="page__title">Planning</h1>
          <p className="page__subtitle">
            Vue des campagnes.
          </p>
        </div>

        <div className="planning-switch">
          <button
            className={view === "timeline" ? "is-active" : ""}
            onClick={() => setView("timeline")}
            type="button"
          >
            Timeline
          </button>
          <button
            className={view === "calendar" ? "is-active" : ""}
            onClick={() => setView("calendar")}
            type="button"
          >
            Calendrier
          </button>
        </div>
      </div>

      <section className="planning-summary">
        <div className="planning-summary__card">
          <div className="planning-summary__label">Campagnes</div>
          <div className="planning-summary__value">{summary.campagnes}</div>
        </div>
        <div className="planning-summary__card">
          <div className="planning-summary__label">Planifiées</div>
          <div className="planning-summary__value">{summary.planifiees}</div>
        </div>
        <div className="planning-summary__card">
          <div className="planning-summary__label">En cours</div>
          <div className="planning-summary__value">{summary.enCours}</div>
        </div>
        <div className="planning-summary__card">
          <div className="planning-summary__label">Priorité critique</div>
          <div className="planning-summary__value">{summary.critiques}</div>
        </div>
      </section>

      <section className="card">
        <h2 className="card__title">Filtres</h2>

        <div className="planning-filters">
          <select
            className="select"
            value={filters.type}
            onChange={(e) => setFilters({ ...filters, type: e.target.value })}
          >
            <option value="campagne">Campagnes</option>
          </select>

          <input
            className="input"
            placeholder="Recherche"
            value={filters.search}
            onChange={(e) => setFilters({ ...filters, search: e.target.value })}
          />

          <select
            className="select"
            value={filters.statut}
            onChange={(e) => setFilters({ ...filters, statut: e.target.value })}
          >
            <option value="">Tous statuts</option>
            <option value="brouillon">Brouillon</option>
            <option value="planifiee">Planifiée</option>
            <option value="en_cours">En cours</option>
            <option value="terminee">Terminée</option>
            <option value="annulee">Annulée</option>
            <option value="a_faire">À faire</option>
            <option value="bloquee">Bloquée</option>
          </select>

          <select
            className="select"
            value={filters.priorite}
            onChange={(e) => setFilters({ ...filters, priorite: e.target.value })}
          >
            <option value="">Toutes priorités</option>
            <option value="basse">Basse</option>
            <option value="moyenne">Moyenne</option>
            <option value="haute">Haute</option>
            <option value="critique">Critique</option>
          </select>

          <input
            className="input"
            type="date"
            value={filters.from}
            onChange={(e) => setFilters({ ...filters, from: e.target.value })}
          />

          <input
            className="input"
            type="date"
            value={filters.to}
            onChange={(e) => setFilters({ ...filters, to: e.target.value })}
          />
        </div>
      </section>

      {loading ? (
        <section className="card">
          <div className="planning-empty">Chargement du planning...</div>
        </section>
      ) : view === "timeline" ? (
        <section className="card">
          <h2 className="card__title">Vue timeline</h2>

          {items.length === 0 ? (
            <div className="planning-empty">Aucun élément dans le planning.</div>
          ) : (
            <div className="list">
              {Object.entries(groupedTimeline).map(([date, bucket]) => (
                <div className="timeline-group" key={date}>
                  <div className="timeline-group__date">
                    {date === "sans-date" ? "Sans date" : formatDateLabel(date)}
                  </div>

                  {bucket.map((item) => (
                    <div className="timeline-item" key={item.uid}>
                      <div className="timeline-item__meta">
                        {item.type === "campagne" ? "Campagne" : "Tâche"}
                      </div>

                      <div>
                        <div className="timeline-item__title">
                          <Link to={item.url}>{item.titre}</Link>
                        </div>

                        <div className="timeline-item__desc">
                          {item.description || "Aucune description"}
                        </div>

                        <div className="timeline-item__tags">
                          <span className={statusBadgeClass(item.statut)}>{item.statut}</span>
                          {item.priorite && (
                            <span className={priorityBadgeClass(item.priorite)}>{item.priorite}</span>
                          )}
                        </div>
                      </div>

                      <div className="timeline-item__meta">
                        {formatTimeRange(item.dateDebut, item.dateFin)}
                      </div>
                    </div>
                  ))}
                </div>
              ))}
            </div>
          )}
        </section>
      ) : (
        <section className="card">
          <div className="calendar-toolbar">
            <button
              className="btn btn--secondary"
              type="button"
              onClick={() =>
                setCurrentMonth(
                  new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1)
                )
              }
            >
              ← Mois précédent
            </button>

            <div className="calendar-toolbar__month">
              {currentMonth.toLocaleDateString("fr-FR", {
                month: "long",
                year: "numeric",
              })}
            </div>

            <button
              className="btn btn--secondary"
              type="button"
              onClick={() =>
                setCurrentMonth(
                  new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1)
                )
              }
            >
              Mois suivant →
            </button>
          </div>

          {items.length === 0 ? (
            <div className="planning-empty">Aucun élément à afficher.</div>
          ) : (
            <div className="calendar-grid">
              {DAYS.map((day) => (
                <div className="calendar-head" key={day}>{day}</div>
              ))}

              {calendarDays.map((day) => {
                const dayItems = items.filter((item) => occursOnDay(item, day));
                const isMuted = day.getMonth() !== currentMonth.getMonth();

                return (
                  <div
                    key={day.toISOString()}
                    className={`calendar-cell ${isMuted ? "is-muted" : ""}`}
                  >
                    <div className="calendar-cell__day">{day.getDate()}</div>

                    {dayItems.map((item) => (
                      <Link
                        key={item.uid}
                        to={item.url}
                        className={`calendar-item calendar-item--${item.type}`}
                        title={`${item.titre} — ${item.statut}`}
                      >
                        {item.titre}
                      </Link>
                    ))}
                  </div>
                );
              })}
            </div>
          )}
        </section>
      )}
    </div>
  );
}