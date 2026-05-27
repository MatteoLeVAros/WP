import { useEffect, useState } from "react";
import { getTaches, createTache, deleteTache } from "../api/tacheApi";
import { Link } from "react-router-dom";

export default function TachesPage() {
  const [taches, setTaches] = useState([]);
  const [filters, setFilters] = useState({
    statut: "",
    priorite: "",
  });

  const [form, setForm] = useState({
    titre: "",
    statut: "a_faire",
    priorite: "",
  });

  const fetchTaches = async () => {
    const data = await getTaches(filters);
    setTaches(data);
  };

  useEffect(() => {
    fetchTaches();
  }, [filters]);

  const handleCreate = async (e) => {
    e.preventDefault();

    await createTache(form);

    setForm({ titre: "", statut: "a_faire", priorite: "",});
    fetchTaches();
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Supprimer cette tâche ?")) return;
return;
    await deleteTache(id);
    fetchTaches();
  };


  return (
    <div>
      <div className="page__header">
        <div>
          <h1 className="page__title">Tâches</h1>
          <p className="page__subtitle">
            Consulte, filtre et crée rapidement les tâches de validation.
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

        <section className="card">
          <h2 className="card__title">Créer une tâche</h2>

          <form className="form-grid" onSubmit={handleCreate}>
            <input
              className="input"
              placeholder="Titre"
              value={form.titre}
              onChange={(e) => setForm({ ...form, titre: e.target.value })}
            />

            <select
              className="select"
              value={form.statut}
              onChange={(e) => setForm({ ...form, statut: e.target.value })}
            >
              <option value="a_faire">À faire</option>
              <option value="en_cours">En cours</option>
              <option value="terminee">Terminée</option>
              <option value="bloquee">Bloquée</option>
            </select>

            <select
              className="select"
              value={form.priorite}
              onChange={(e) => setForm({ ...form, priorite: e.target.value })}
            >
              <option value="">Priorité</option>
              <option value="basse">Basse</option>
              <option value="moyenne">Moyenne</option>
              <option value="haute">Haute</option>
              <option value="critique">Critique</option>
            </select>

            <button className="btn btn--primary" type="submit">
              Créer
            </button>
          </form>
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
