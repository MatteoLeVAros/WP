import { useEffect, useState } from "react";
import { getTaches, deleteTache } from "../api/tacheApi";
import { Link } from "react-router-dom";

export default function TachesPage() {
  const [taches, setTaches] = useState([]);
  const [filters, setFilters] = useState({
    statut: "",
    priorite: "",
  });

  const fetchTaches = async () => {
    const data = await getTaches(filters);
    setTaches(data);
  };

  useEffect(() => {
    fetchTaches();
  }, [filters]);


  const handleDelete = async (id) => {
    const confirmed = window.confirm("Supprimer cette tâche ?");
    console.log("Suppression confirmée ?", confirmed, "id =", id);

    if (!confirmed) return;

    try {
      console.log("Envoi DELETE pour tâche", id);
      const res = await deleteTache(id);
      console.log("DELETE OK :", res);

      await fetchTaches();
      console.log("Liste des tâches rechargée");
    } catch (error) {
      console.error("Erreur suppression tâche :", error);
      console.error("Status :", error.response?.status);
      console.error("Réponse backend :", error.response?.data);

      alert(
        error.response?.data?.message ||
        error.response?.data?.detail ||
        `Erreur lors de la suppression (HTTP ${error.response?.status || "?"})`
      );
    }
  };


  return (
    <div>
      <div className="page__header">
        <div>
          <h1 className="page__title">Mes Tâches</h1>
          <p className="page__subtitle">
            Consulte et filtre tes tâches de validation.
          </p>
        </div>
      </div>

      <div className="grid grid--2">
        <section className="card">
          <h2 className="card__title">Filtres</h2>

          <div className="toolbar">
            <select
              className="select"
              value={filters.statut}
              onChange={(e) =>
                setFilters({ ...filters, statut: e.target.value })
              }
            >
              <option value="">Tous statuts</option>
              <option value="a_faire">À faire</option>
              <option value="en_cours">En cours</option>
              <option value="terminee">Terminée</option>
              <option value="bloquee">Bloquée</option>
            </select>

            <select
              className="select"
              value={filters.priorite}
              onChange={(e) =>
                setFilters({ ...filters, priorite: e.target.value })
              }
            >
              <option value="">Toutes priorités</option>
              <option value="basse">Basse</option>
              <option value="moyenne">Moyenne</option>
              <option value="haute">Haute</option>
              <option value="critique">Critique</option>
            </select>
          </div>
        </section>
      </div>

      <section className="card" style={{ marginTop: 20 }}>
        <h2 className="card__title">Liste des tâches</h2>

        {taches.length === 0 ? (
          <div className="empty-state">Aucune tâche trouvée.</div>
        ) : (
          <div className="table-wrap">
            <table className="table">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Statut</th>
                  <th>Priorité</th>
                  <th>Date création</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                {taches.map((tache) => (
                  <tr key={tache.id}>
                    <td>
                      <Link to={`/taches/${tache.id}`}>{tache.titre}</Link>
                    </td>
                    <td>{tache.statut}</td>
                    <td>{tache.priorite || "-"}</td>
                    <td>
                      {tache.dateCreation
                        ? new Date(tache.dateCreation).toLocaleString()
                        : "-"}
                    </td>
                    <td>
                      <button
                        className="btn btn--danger"
                        onClick={() => handleDelete(tache.id)}
                      >
                        Supprimer
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </section>
    </div>
  );
}
