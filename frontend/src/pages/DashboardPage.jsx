import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { getCampagnes } from "../api/campagneApi";
import { useAuth } from "../context/AuthContext";

export default function DashboardPage() {
  const { user } = useAuth();

  const [campagnes, setCampagnes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const isAdmin = user?.roles?.includes("ROLE_ADMIN");

  useEffect(() => {
    const fetchDashboard = async () => {
      try {
        setLoading(true);
        setError("");

        const data = await getCampagnes();
        setCampagnes(data);
      } catch (e) {
        console.error("Erreur chargement dashboard :", e);
        setError("Impossible de charger les données du dashboard.");
      } finally {
        setLoading(false);
      }
    };

    fetchDashboard();
  }, []);

  const formatDate = (dateString) => {
    if (!dateString) return "Non renseignée";

    return new Date(dateString).toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

  const countByStatus = (status) => {
    return campagnes.filter((campagne) => campagne.statut === status).length;
  };

  const countByPriority = (priority) => {
    return campagnes.filter((campagne) => campagne.priorite === priority).length;
  };

  const getStatusBadgeClass = (statut) => {
    switch (statut) {
      case "planifiee":
        return "badge badge--blue";
      case "en_cours":
        return "badge badge--orange";
      case "terminee":
        return "badge badge--green";
      case "annulee":
        return "badge badge--red";
      default:
        return "badge badge--blue";
    }
  };

  const prochainesCampagnes = [...campagnes]
    .filter((campagne) => campagne.dateDebutPrevue)
    .sort(
      (a, b) =>
        new Date(a.dateDebutPrevue).getTime() -
        new Date(b.dateDebutPrevue).getTime()
    )
    .slice(0, 5);

  if (loading) {
    return (
      <div className="page">
        <p>Chargement du dashboard...</p>
      </div>
    );
  }

  return (
    <div className="page">
      <div className="page__header">
        <div>
          <h1 className="page__title">Dashboard</h1>
          <p className="page__subtitle">
            Bonjour {user?.prenom || user?.email}, voici une vue d’ensemble de
            l’activité.
          </p>
        </div>

        <div className="quick-actions">
          <Link to="/campagnes">
            <button className="btn btn--primary">Voir les campagnes</button>
          </Link>

          {isAdmin && (
            <Link to="/profile">
              <button className="btn btn--secondary">Gérer les profils</button>
            </Link>
          )}
        </div>
      </div>

      {error && <p className="empty-state">{error}</p>}

      <section className="grid dashboard-stats">
        <div className="card stat-card">
          <p className="stat-card__label">Campagnes totales</p>
          <p className="stat-card__value">{campagnes.length}</p>
          <p className="stat-card__hint">Toutes campagnes confondues</p>
        </div>

        <div className="card stat-card">
          <p className="stat-card__label">Planifiées</p>
          <p className="stat-card__value">{countByStatus("planifiee")}</p>
          <p className="stat-card__hint">À venir ou programmées</p>
        </div>

        <div className="card stat-card">
          <p className="stat-card__label">En cours</p>
          <p className="stat-card__value">{countByStatus("en_cours")}</p>
          <p className="stat-card__hint">Campagnes actives</p>
        </div>

        <div className="card stat-card">
          <p className="stat-card__label">Terminées</p>
          <p className="stat-card__value">{countByStatus("terminee")}</p>
          <p className="stat-card__hint">Campagnes finalisées</p>
        </div>
      </section>

      <section className="grid dashboard-layout">
        <div className="card">
          <h2 className="card__title">Prochaines campagnes</h2>

          {prochainesCampagnes.length > 0 ? (
            <div className="list">
              {prochainesCampagnes.map((campagne) => (
                <div key={campagne.id} className="list-item campaign-item">
                  <div>
                    <p className="campaign-item__title">{campagne.titre}</p>

                    <p className="campaign-item__meta">
                      Référence : {campagne.referenceCampagne}
                    </p>

                    <p className="campaign-item__meta">
                      Début prévu : {formatDate(campagne.dateDebutPrevue)}
                    </p>

                    <p className="campaign-item__meta">
                      Priorité : {campagne.priorite || "Non renseignée"}
                    </p>
                  </div>

                  <div>
                    <span className={getStatusBadgeClass(campagne.statut)}>
                      {campagne.statut || "Non renseigné"}
                    </span>

                    <div style={{ marginTop: 12 }}>
                      <Link to={`/campagnes/${campagne.id}`}>
                        <button className="btn btn--secondary">
                          Détail
                        </button>
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="empty-state">Aucune campagne prévue.</p>
          )}
        </div>

        <div className="card">
          <h2 className="card__title">Synthèse</h2>

          <div className="kpi-row">
            <span className="kpi-row__label">Planifiées</span>
            <span className="kpi-row__value">{countByStatus("planifiee")}</span>
          </div>

          <div className="kpi-row">
            <span className="kpi-row__label">En cours</span>
            <span className="kpi-row__value">{countByStatus("en_cours")}</span>
          </div>

          <div className="kpi-row">
            <span className="kpi-row__label">Terminées</span>
            <span className="kpi-row__value">{countByStatus("terminee")}</span>
          </div>

          <div className="kpi-row">
            <span className="kpi-row__label">Priorité haute</span>
            <span className="kpi-row__value">{countByPriority("haute")}</span>
          </div>

          <div className="kpi-row">
            <span className="kpi-row__label">Sans priorité</span>
            <span className="kpi-row__value">
              {campagnes.filter((campagne) => !campagne.priorite).length}
            </span>
          </div>
        </div>
      </section>
    </div>
  );
}