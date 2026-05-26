import { useEffect, useState } from "react";
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
      dateDebutPrevue: "",
      dateFinPrevue: "",
      responsableId: "",
    });

    fetchCampagnes();
  };

  const handleDelete = async (id) => {
    if (!confirm("Supprimer cette campagne ?")) return;

    await deleteCampagne(id);
    fetchCampagnes();
  };

  return (
    <div style={{ padding: 20 }}>
      <h1>Campagnes</h1>

      {/* FILTRES */}
      <div>
        <select onChange={(e) => setFilters({ ...filters, statut: e.target.value })}>
          <option value="">Tous statuts</option>
          <option value="brouillon">Brouillon</option>
          <option value="planifiee">Planifiée</option>
          <option value="en_cours">En cours</option>
          <option value="terminee">Terminée</option>
          <option value="annulee">Annulée</option>
        </select>

        <select onChange={(e) => setFilters({ ...filters, priorite: e.target.value })}>
          <option value="">Toutes priorités</option>
          <option value="basse">Basse</option>
          <option value="moyenne">Moyenne</option>
          <option value="haute">Haute</option>
          <option value="critique">Critique</option>
        </select>
      </div>

      {/* FORMULAIRE */}
      <form onSubmit={handleCreate} style={{ marginTop: 20 }}>
        <input
          placeholder="Référence"
          value={form.referenceCampagne}
          onChange={(e) =>
            setForm({ ...form, referenceCampagne: e.target.value })
          }
        />

        <input
          placeholder="Titre"
          value={form.titre}
          onChange={(e) => setForm({ ...form, titre: e.target.value })}
        />

        <select
          value={form.statut}
          onChange={(e) => setForm({ ...form, statut: e.target.value })}
        >
          <option value="brouillon">Brouillon</option>
          <option value="planifiee">Planifiée</option>
          <option value="en_cours">En cours</option>
          <option value="terminee">Terminée</option>
          <option value="annulee">Annulée</option>
        </select>

        <input
          type="datetime-local"
          value={form.dateDebutPrevue}
          onChange={(e) =>
            setForm({ ...form, dateDebutPrevue: e.target.value })
          }
        />

        <input
          type="datetime-local"
          value={form.dateFinPrevue}
          onChange={(e) =>
            setForm({ ...form, dateFinPrevue: e.target.value })
          }
        />

        <input
          placeholder="Responsable ID"
          value={form.responsableId}
          onChange={(e) =>
            setForm({ ...form, responsableId: e.target.value })
          }
        />

        <button type="submit">Créer</button>
      </form>

      {/* LISTE */}
      <ul style={{ marginTop: 20 }}>
        {campagnes.map((c) => (
          <li key={c.id}>
            <strong>{c.titre}</strong> ({c.statut})
            <button onClick={() => handleDelete(c.id)}>Supprimer</button>
          </li>
        ))}
      </ul>
    </div>
  );
}