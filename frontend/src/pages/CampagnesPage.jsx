import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {
  getCampagnes,
  createCampagne,
  deleteCampagne,
} from "../api/campagneApi";
import { getDemandesIntervention } from "../api/demandeInterventionApi";
import { getUsers } from "../api/userApi";

export default function CampagnesPage() {
  const [campagnes, setCampagnes] = useState([]);
  const [demandes, setDemandes] = useState([]);
  const [users, setUsers] = useState([]);
  const [selectedDemande, setSelectedDemande] = useState(null);

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
    description: "",
    commentaireGlobal: "",
    demandeInterventionId: "",
  });

  const fetchCampagnes = async () => {
    const data = await getCampagnes(filters);
    setCampagnes(data);
  };

  const fetchDemandes = async () => {
    const data = await getDemandesIntervention();
    setDemandes(data);
  };

  const fetchUsers = async () => {
  try {
    const data = await getUsers();
    setUsers(data);
  } catch (error) {
    console.error("Erreur lors du chargement des utilisateurs :", error);
  }
  };

  useEffect(() => {
    fetchCampagnes();
  }, [filters]);

  useEffect(() => {
    fetchDemandes();
    fetchUsers();
  }, []);


  const handleSelectDemande = (demande) => {
    setSelectedDemande(demande);

    setForm((prev) => ({
      ...prev,
      demandeInterventionId: demande.id,
      titre: prev.titre || `Campagne - ${demande.typeIntervention || "Intervention"}`,
      referenceCampagne:
        prev.referenceCampagne || `CAMP-${String(demande.id).padStart(4, "0")}`,
      description:
        prev.description ||
        `Projet/Moyen : ${demande.projetNumeroMoyenValidation || "-"} | Système : ${demande.systeme || "-"}`,
    }));
  };

  const resetForm = () => {
    setForm({
      referenceCampagne: "",
      titre: "",
      statut: "brouillon",
      priorite: "",
      dateDebutPrevue: "",
      dateFinPrevue: "",
      responsableId: "",
      description: "",
      commentaireGlobal: "",
      demandeInterventionId: "",
    });
    setSelectedDemande(null);
  };

  const handleCreate = async (e) => {
    e.preventDefault();

    try {
      await createCampagne(form);
      resetForm();
      await fetchCampagnes();
      await fetchDemandes();
    } catch (error) {
      console.error("Erreur lors de la création de campagne :", error);
      console.error("Réponse backend :", error.response?.data);
    }
  };

  const handleDelete = async (id) => {
    const confirmed = window.confirm("Supprimer cette campagne ?");
    console.log("Suppression confirmée ?", confirmed, "id =", id);

    if (!confirmed) return;

    try {
      console.log("Envoi DELETE pour campagne", id);
      const res = await deleteCampagne(id);
      console.log("DELETE OK :", res);

      await fetchCampagnes();
      console.log("Liste des campagnes rechargée");
    } catch (error) {
      console.error("Erreur suppression campagne :", error);
      console.error("Status :", error.response?.status);
      console.error("Réponse backend :", error.response?.data);

      alert(
        error.response?.data?.message ||
        `Erreur lors de la suppression (HTTP ${error.response?.status || "?"})`
      );
    }
  };

  return (
    <div>
      <div className="page__header">
        <div>
          <h1 className="page__title">Campagnes</h1>
          <p className="page__subtitle">
            Assigner les demandes d’intervention et piloter les campagnes.
          </p>
        </div>
      </div>

      {/* Filtres campagnes */}
      <section className="card" style={{ marginBottom: 20 }}>
        <h2 className="card__title">Filtres campagnes</h2>

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

      {/* Les 2 blocs */}
      <div className="grid grid--2">
        {/* Bloc 1 */}
        <section className="card">
          <h2 className="card__title">Assigner une campagne</h2>

          {selectedDemande ? (
            <div
              style={{
                marginBottom: 16,
                padding: 12,
                border: "1px solid #ddd",
                borderRadius: 8,
                background: "#f9f9f9",
              }}
            >
              <strong>Demande sélectionnée :</strong>
              <div>ID : {selectedDemande.id}</div>
              <div>Type : {selectedDemande.typeIntervention || "-"}</div>
              <div>Système : {selectedDemande.systeme || "-"}</div>
              <div>
                Projet / Moyen : {selectedDemande.projetNumeroMoyenValidation || "-"}
              </div>
              <div>Statut : {selectedDemande.statutDemande || "-"}</div>
            </div>
          ) : (
            <div className="empty-state" style={{ marginBottom: 16 }}>
              Sélectionne une demande dans le bloc de droite.
            </div>
          )}

          <form className="form-grid" onSubmit={handleCreate}>
            <input
              className="input"
              placeholder="Référence campagne"
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

            <select
              className="select"
              value={form.responsableId}
              onChange={(e) =>
                setForm({ ...form, responsableId: e.target.value })
              }
            >
              <option value="">Choisir un IS</option>
              {users.map((u) => (
                <option key={u.id} value={u.id}>
                   {u.prenom} {u.nom}
                </option>
              ))}
            </select>

            <textarea
              className="input"
              placeholder="Description"
              value={form.description}
              onChange={(e) =>
                setForm({ ...form, description: e.target.value })
              }
            />

            <textarea
              className="input"
              placeholder="Commentaire global"
              value={form.commentaireGlobal}
              onChange={(e) =>
                setForm({ ...form, commentaireGlobal: e.target.value })
              }
            />

            <input
              className="input"
              placeholder="Demande intervention ID"
              value={form.demandeInterventionId}
              readOnly
            />

            <div style={{ display: "flex", gap: 8 }}>
              <button
                className="btn btn--primary"
                type="submit"
                disabled={!form.demandeInterventionId || !form.responsableId}
              >
                Assigner la campagne
              </button>

              <button
                className="btn"
                type="button"
                onClick={resetForm}
              >
                Réinitialiser
              </button>
            </div>
          </form>
        </section>

        {/* Bloc 2 */}
        <section className="card">
          <h2 className="card__title">Toutes les demandes d’intervention</h2>

          {demandes.length === 0 ? (
            <div className="empty-state">Aucune demande trouvée.</div>
          ) : (
            <div className="table-wrap">
              <table className="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Système</th>
                    <th>Projet / Moyen</th>
                    <th>Statut</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  {demandes.map((d) => (
                    <tr key={d.id}>
                      <td>{d.id}</td>
                      <td>{d.typeIntervention || "-"}</td>
                      <td>{d.systeme || "-"}</td>
                      <td>{d.projetNumeroMoyenValidation || "-"}</td>
                      <td>{d.statutDemande || "-"}</td>
                      <td>
                        <button
                          className="btn btn--primary"
                          onClick={() => handleSelectDemande(d)}
                        >
                          Assigner
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

      {/* Liste des campagnes */}
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