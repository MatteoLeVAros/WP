import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getTache } from "../api/tacheApi";
import {
  getCommentairesByCampagne,
  createCommentaire,
  deleteCommentaire,
} from "../api/commentaireApi";
import API from "../api/axios";

export default function TacheDetailPage() {
  const { id } = useParams();

  const [tache, setTache] = useState(null);
  const [commentaires, setCommentaires] = useState([]);
  const [contenu, setContenu] = useState("");

  const fetchData = async () => {
    const t = await getTache(id);
    setTache(t);

    const res = await API.get(`/commentaires?tache=${id}`);
    setCommentaires(res.data);
  };

  useEffect(() => {
    fetchData();
  }, [id]);

  const handleCreateCommentaire = async (e) => {
    e.preventDefault();

    await createCommentaire({
      contenu,
      tacheId: id,
    });

    setContenu("");
    fetchData();
  };

  const handleDelete = async (id) => {
    if (!confirm("Supprimer ce commentaire ?")) return;

    await deleteCommentaire(id);
    fetchData();
  };

  if (!tache) return <div>Loading...</div>;

  return (
    <div style={{ padding: 20 }}>
      <h1>{tache.titre}</h1>

      <p>Statut: {tache.statut}</p>
      <p>Priorité: {tache.priorite}</p>

      <hr />

      <h2>Commentaires</h2>

      <form onSubmit={handleCreateCommentaire}>
        <input
          placeholder="Ajouter un commentaire"
          value={contenu}
          onChange={(e) => setContenu(e.target.value)}
        />

        <button type="submit">Envoyer</button>
      </form>

      <ul>
        {commentaires.map((c) => (
          <li key={c.id}>
            <strong>{c.auteur?.nom}</strong> : {c.contenu}
            <button onClick={() => handleDelete(c.id)}>X</button>
          </li>
        ))}
      </ul>
    </div>
  );
}