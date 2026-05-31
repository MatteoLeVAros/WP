import { useEffect, useState } from "react";
import {  getDemandesIntervention,  createDemandeIntervention, cancelDemandeIntervention,
} from "../api/demandeInterventionApi";

export default function DemandesInterventionPage() {
  const [demandes, setDemandes] = useState([]);
  const [form, setForm] = useState({
    typeIntervention: "",
    projetNumeroMoyenValidation: "",
    systeme: "",
    emplacementMoyenBadge: "",
    dureeIntervention: "",
    dateDemarrageSouhaitee: "",
    dateLimiteLivraison: "",
    nombreIntervenants: 1,
    besoinConducteurPermisC: "",
    lienStockagePvalLogs: "",
    statutInstrumentation: "",
    lienTemplateChecklist: "",
    versionSwValider: "",
    canEnregistrer: "",
  });

  const fetchDemandes = async () => {
    try {
      const data = await getDemandesIntervention();
      setDemandes(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Erreur fetch :", error);
    }
  };

  useEffect(() => {
    fetchDemandes();
  }, []);

  const handleCreate = async (e) => {
    e.preventDefault();

    const payload = {
      ...form,
      nombreIntervenants: parseInt(form.nombreIntervenants, 10) || 1,
      dateDemarrageSouhaitee: form.dateDemarrageSouhaitee ? `${form.dateDemarrageSouhaitee}T00:00:00Z` : null,
      dateLimiteLivraison: form.dateLimiteLivraison ? `${form.dateLimiteLivraison}T00:00:00Z` : null,
    };

    try {
      await createDemandeIntervention(payload);

      setForm({
        typeIntervention: "",
        projetNumeroMoyenValidation: "",
        systeme: "",
        emplacementMoyenBadge: "",
        dureeIntervention: "",
        dateDemarrageSouhaitee: "",
        dateLimiteLivraison: "",
        nombreIntervenants: 1,
        besoinConducteurPermisC: "",
        lienStockagePvalLogs: "",
        statutInstrumentation: "",
        lienTemplateChecklist: "",
        versionSwValider: "",
        canEnregistrer: "",
      });

      await fetchDemandes();
    } catch (error) {
      console.error("Erreur lors de la création :", error);
    }
  };

  const handleCancel = async (id) => {
    const confirmed = window.confirm("Voulez-vous vraiment annuler cette demande ?");
    if (!confirmed) return;

    try {
      await cancelDemandeIntervention(id);
      await fetchDemandes();
    } catch (error) {
      console.error("Erreur lors de l’annulation :", error);
      console.error("Status :", error.response?.status);
      console.error("Réponse backend :", error.response?.data);

      alert(
        error.response?.data?.message ||
        error.response?.data?.detail ||
        "Erreur lors de l’annulation"
      );
    }
  };

  return (
    <div>
      <div className="page__header">
        <div>
          <h1 className="page__title">Demandes d’intervention</h1>
          <p className="page__subtitle">
            Saisie initiale par l’ingénieur avant planification par le gestionnaire.
          </p>
        </div>
      </div>

      <section className="card">
        <h2 className="card__title">Nouvelle demande</h2>

        <form className="form-grid" onSubmit={handleCreate}>
          <input className="input" placeholder="Type d’intervention" value={form.typeIntervention}
            onChange={(e) => setForm({ ...form, typeIntervention: e.target.value })} />

          <input className="input" placeholder="Projet / Num moyen validation" value={form.projetNumeroMoyenValidation}
            onChange={(e) => setForm({ ...form, projetNumeroMoyenValidation: e.target.value })} />

          <input className="input" placeholder="Système" value={form.systeme}
            onChange={(e) => setForm({ ...form, systeme: e.target.value })} />

          <input className="input" placeholder="Emplacement moyen / badge" value={form.emplacementMoyenBadge}
            onChange={(e) => setForm({ ...form, emplacementMoyenBadge: e.target.value })} />

          <input className="input" placeholder="Durée intervention" value={form.dureeIntervention}
            onChange={(e) => setForm({ ...form, dureeIntervention: e.target.value })} />

          <input className="input" type="date" value={form.dateDemarrageSouhaitee}
            onChange={(e) => setForm({ ...form, dateDemarrageSouhaitee: e.target.value })} />

          <input className="input" type="date" value={form.dateLimiteLivraison}
            onChange={(e) => setForm({ ...form, dateLimiteLivraison: e.target.value })} />

          <input className="input" type="number" min="1" placeholder="Nombre intervenants" value={form.nombreIntervenants}
            onChange={(e) => setForm({ ...form, nombreIntervenants: e.target.value })} />

          <input className="input" placeholder="Besoin conducteur Permis C" value={form.besoinConducteurPermisC}
            onChange={(e) => setForm({ ...form, besoinConducteurPermisC: e.target.value })} />

          <input className="input" placeholder="Lien stockage PVAL / Logs" value={form.lienStockagePvalLogs}
            onChange={(e) => setForm({ ...form, lienStockagePvalLogs: e.target.value })} />

          <input className="input" placeholder="Statut instrumentation" value={form.statutInstrumentation}
            onChange={(e) => setForm({ ...form, statutInstrumentation: e.target.value })} />

          <input className="input" placeholder="Lien template checklist" value={form.lienTemplateChecklist}
            onChange={(e) => setForm({ ...form, lienTemplateChecklist: e.target.value })} />

          <input className="input" placeholder="Version SW à valider" value={form.versionSwValider}
            onChange={(e) => setForm({ ...form, versionSwValider: e.target.value })} />

          <input className="input" placeholder="CAN à enregistrer" value={form.canEnregistrer}
            onChange={(e) => setForm({ ...form, canEnregistrer: e.target.value })} />

          <button className="btn btn--primary" type="submit">
            Envoyer la demande
          </button>
        </form>
      </section>

      <section className="card" style={{ marginTop: 20 }}>
        <h2 className="card__title">Mes demandes</h2>

        {demandes.length === 0 ? (
          <div className="empty-state">Aucune demande pour le moment.</div>
        ) : (
          <div className="table-wrap">
            <table className="table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Système</th>
                  <th>Démarrage souhaité</th>
                  <th>Livraison</th>
                  <th>Statut</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                {demandes.map((d) => (
                  <tr key={d.id}>
                    <td>{d.typeIntervention || "N/A"}</td>
                    <td>{d.systeme || "N/A"}</td>
                    <td>
                      {d.dateDemarrageSouhaitee
                        ? new Date(d.dateDemarrageSouhaitee).toLocaleDateString("fr-FR")
                        : "-"}
                    </td>
                    <td>
                      {d.dateLimiteLivraison
                        ? new Date(d.dateLimiteLivraison).toLocaleDateString("fr-FR")
                        : "-"}
                    </td>
                    <td>{d.statutDemande || "-"}</td>
                    <td>
                      {d.statutDemande === "soumise" && (
                          <button type="button" className="btn btn--secondary"  onClick={() => handleCancel(d.id)}>
                            Annuler
                          </button>
                        )}
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