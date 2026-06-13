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

  const formatDate = (dateString) => {
    if (!dateString) return "Non renseignée";

    return new Date(dateString).toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

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
    <div className="page">
      <h1>{campagne.titre}</h1>

      <p>
        <strong>Statut :</strong> {campagne.statut || "Non renseigné"}
      </p>
      <p>
        <strong>Priorité :</strong> {campagne.priorite || "Non renseignée"}
      </p>
      <p>
        <strong>Référence :</strong> {campagne.referenceCampagne}
      </p>

      <hr />

      <h2>Demandes d’intervention liées</h2>

      {campagne.demandeInterventions?.length > 0 ? (
        <div>
          {campagne.demandeInterventions.map((d) => (
            <div
              key={d.id}
              style={{
                marginBottom: 20,
                padding: 16,
                border: "1px solid #ddd",
                borderRadius: 8,
                backgroundColor: "#fafafa",
              }}
            >
              <h3 style={{ marginTop: 0 }}>
                Demande #{d.id} — {d.typeIntervention || "Type non renseigné"}
              </h3>

              <p>
                <strong>Statut :</strong>{" "}
                {d.statutDemande || "Non renseigné"}
              </p>

              <p>
                <strong>Type d’intervention :</strong>{" "}
                {d.typeIntervention || "Non renseigné"}
              </p>

              <p>
                <strong>Système :</strong> {d.systeme || "Non renseigné"}
              </p>

              <p>
                <strong>Projet / numéro moyen de validation :</strong>{" "}
                {d.projetNumeroMoyenValidation || "Non renseigné"}
              </p>

              <p>
                <strong>Emplacement moyen / badge :</strong>{" "}
                {d.emplacementMoyenBadge || "Non renseigné"}
              </p>

              <p>
                <strong>Date de démarrage souhaitée :</strong>{" "}
                {formatDate(d.dateDemarrageSouhaitee)}
              </p>

              <p>
                <strong>Date limite de livraison :</strong>{" "}
                {formatDate(d.dateLimiteLivraison)}
              </p>

              <hr />

              <h4>Contact demandeur</h4>

              {d.demandeur ? (
                <div>
                  <p>
                    <strong>Nom :</strong>{" "}
                    {d.demandeur.prenom || ""} {d.demandeur.nom || ""}
                  </p>

                  <p>
                    <strong>Email :</strong>{" "}
                    {d.demandeur.email ? (
                      <a href={`mailto:${d.demandeur.email}`}>
                        {d.demandeur.email}
                      </a>
                    ) : (
                      "Non renseigné"
                    )}
                  </p>

                  <p>
                    <strong>Téléphone :</strong>{" "}
                    {d.demandeur.telephone || "Non renseigné"}
                  </p>

                  <p>
                    <strong>Fonction :</strong>{" "}
                    {d.demandeur.fonction || "Non renseignée"}
                  </p>

                  <p>
                    <strong>Disponibilité :</strong>{" "}
                    {d.demandeur.disponibilite
                      ? "Disponible"
                      : "Non disponible"}
                  </p>
                </div>
              ) : (
                <p>Aucun demandeur associé.</p>
              )}
            </div>
          ))}
        </div>
      ) : (
        <p>Aucune demande liée à cette campagne.</p>
      )}

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