import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { getCampagne } from "../api/campagneApi";
import {
  getCommentairesByCampagne,
  createCommentaire,
  deleteCommentaire,
} from "../api/commentaireApi";

export default function CampagneDetailPage() {
  const { id } = useParams();

  const [campagne, setCampagne] = useState(null);
  const [commentaires, setCommentaires] = useState([]);
  const [contenu, setContenu] = useState("");

  const fetchData = async () => {
    const c = await getCampagne(id);
    setCampagne(c);

    const coms = await getCommentairesByCampagne(id);
    setCommentaires(coms);
  };

  useEffect(() => {
    fetchData();
  }, [id]);

  const handleCreateCommentaire = async (e) => {
    e.preventDefault();

    await createCommentaire({
      contenu,
      campagneId: id,
    });

    setContenu("");
    fetchData();
  };

  const handleDelete = async (id) => {
    if (!confirm("Supprimer ce commentaire ?")) return;

    await deleteCommentaire(id);
    fetchData();
  };

  if (!campagne) return <div>Loading...</div>;

  return (
    <div style={{ padding: 20 }}>
      <h1>{campagne.titre}</h1>

      <p>Statut: {campagne.statut}</p>
      <p>Priorité: {campagne.priorite}</p>
      <p>Référence: {campagne.referenceCampagne}</p>

      <hr />
      <hr />

      <h2>Demandes d’intervention liées</h2>

      {campagne.demandeInterventions?.length > 0 ? (
        <ul>
          {campagne.demandeInterventions.map((d) => (
            <li key={d.id}>
              <strong>{d.typeIntervention}</strong> — {d.systeme} —{" "}
              {d.projetNumeroMoyenValidation} — statut : {d.statutDemande}
            </li>
          ))}
        </ul>
      ) : (
        <p>Aucune demande liée à cette campagne.</p>
      )}


      {/* ✅ Commentaires */}
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