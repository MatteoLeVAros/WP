import { useEffect, useState } from "react";
import { getTaches, createTache, deleteTache } from "../api/tacheApi";

export default function TachesPage() {
  const [taches, setTaches] = useState([]);
  const [filters, setFilters] = useState({
    statut: "",
    priorite: "",
  });

  const [form, setForm] = useState({
    titre: "",
    statut: "a_faire",
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

    setForm({ titre: "", statut: "a_faire" });
    fetchTaches();
  };

  const handleDelete = async (id) => {
    if (!confirm("Supprimer cette tâche ?")) return;

    await deleteTache(id);
    fetchTaches();
  };

  return (
    <div style={{ padding: 20 }}>
      <h1>Tâches</h1>

      {/* FILTRES */}
      <div>
        <select onChange={(e) => setFilters({ ...filters, statut: e.target.value })}>
          <option value="">Tous statuts</option>
          <option value="a_faire">À faire</option>
          <option value="en_cours">En cours</option>
          <option value="terminee">Terminée</option>
          <option value="bloquee">Bloquée</option>
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
          placeholder="Titre"
          value={form.titre}
          onChange={(e) => setForm({ ...form, titre: e.target.value })}
        />

        <select
          value={form.statut}
          onChange={(e) => setForm({ ...form, statut: e.target.value })}
        >
          <option value="a_faire">À faire</option>
          <option value="en_cours">En cours</option>
          <option value="terminee">Terminée</option>
          <option value="bloquee">Bloquée</option>
        </select>

        <button type="submit">Créer</button>
      </form>

      {/* LISTE */}
      <ul style={{ marginTop: 20 }}>
        {taches.map((tache) => (
          <li key={tache.id}>
            <strong>{tache.titre}</strong> ({tache.statut})
            <button onClick={() => handleDelete(tache.id)}>Supprimer</button>
          </li>
        ))}
      </ul>
    </div>
  );
}
