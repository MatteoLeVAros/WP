import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {
  getCampagnes,
  createCampagne,
  deleteCampagne,
} from "../api/campagneApi";

export default function CampagnesPage() {
  const [campagnes, setCampagnes] = useState([]);
  const [filters, setFilters] = useState({
    statut: "",
    priorite: "",
  });

  const [form, setForm] = useState({
    referenceCampagne: "",
    titre: "",
    statut: "brouillon",
    priorite: "",
    dateDebutPrevue: "",
    dateFinPrevue: "",
    responsableId: "",
  });

  const fetchCampagnes = async () => {
    const data = await getCampagnes(filters);
    setCampagnes(data);
  };

  useEffect(() => {
    fetchCampagnes();
  }, [filters]);

  const handleCreate = async (e) => {
    e.preventDefault();

    await createCampagne(form);

    setForm({
      referenceCampagne: "",
      titre: "",
      statut: "brouillon",
      priorite: "",
      dateDebutPrevue: "",
      dateFinPrevue: "",
      responsableId: "",
    });

    fetchCampagnes();
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Supprimer cette campagne ?")) return;
    await deleteCampagne(id);
    fetchCampagnes();
  };

  return (
    <div>
      <div className="page__header">
        <div>
          <h1 className="page__title">Campagnes</h1>
          <p className="page__subtitle">
            Organise, filtre et pilote les campagnes de validation.
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
              <option value="brouillon">Brouillon</option>
              <option value="planifiee">Planifiée</option>
              <option value="en_cours">En cours</option>
              <option value="terminee">Terminée</option>
              <option value="annulee">Annulée</option>
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
          <h2 className="card__title">Créer une campagne</h2>

          <form className="form-grid" onSubmit={handleCreate}>
            <input
              className="input"
              placeholder="Référence"
              value={form.referenceCampagne}
              onChange={(e) =>
                setForm({ ...form, referenceCampagne: e.target.value })
              }
            />

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
              <option value="brouillon">Brouillon</option>
              <option value="planifiee">Planifiée</option>
              <option value="en_cours">En cours</option>
              <option value="terminee">Terminée</option>
              <option value="annulee">Annulée</option>
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

            <input
              className="input"
              type="datetime-local"
              value={form.dateDebutPrevue}
              onChange={(e) =>
                setForm({ ...form, dateDebutPrevue: e.target.value })
              }
            />

            <input
              className="input"
              type="datetime-local"
              value={form.dateFinPrevue}
              onChange={(e) =>
                setForm({ ...form, dateFinPrevue: e.target.value })
              }
            />

            <input
              className="input"
              placeholder="Responsable ID"
              value={form.responsableId}
              onChange={(e) =>
                setForm({ ...form, responsableId: e.target.value })
              }
            />

            <button className="btn btn--primary" type="submit">
              Créer
            </button>
          </form>
        </section>
      </div>

      <section className="card" style={{ marginTop: 20 }}>
        <h2 className="card__title">Liste des campagnes</h2>

        {campagnes.length === 0 ? (
          <div className="empty-state">Aucune campagne trouvée.</div>
        ) : (
          <div className="table-wrap">
            <table className="table">
              <thead>
                <tr>
                  <th>Référence</th>
                  <th>Titre</th>
                  <th>Statut</th>
                  <th>Priorité</th>
                  <th>Date création</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                {campagnes.map((c) => (
                  <tr key={c.id}>
                    <td>{c.referenceCampagne}</td>
                    <td>
                      <Link to={`/campagnes/${c.id}`}>{c.titre}</Link>
                    </td>
                    <td>{c.statut}</td>
                    <td>{c.priorite || "-"}</td>
                    <td>
                      {c.dateCreation
                        ? new Date(c.dateCreation).toLocaleString()
                        : "-"}
                    </td>
                    <td>
                      <button
                        className="btn btn--danger"
                        onClick={() => handleDelete(c.id)}
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