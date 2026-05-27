import { useEffect, useState } from "react";
import {
  getDemandesIntervention,
  createDemandeIntervention,
  deleteDemandeIntervention,
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
    const data = await getDemandesIntervention();
    setDemandes(data);
  };

  useEffect(() => {
    fetchDemandes();
  }, []);

  const handleCreate = async (e) => {
    e.preventDefault();
    await createDemandeIntervention(form);

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

    fetchDemandes();
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Supprimer cette demande ?")) return;
    await deleteDemandeIntervention(id);
    fetchDemandes();
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
                    <td>{d.typeIntervention}</td>
                    <td>{d.systeme}</td>
                    <td>{d.dateDemarrageSouhaitee}</td>
                    <td>{d.dateLimiteLivraison}</td>
                    <td>{d.statutDemande}</td>
                    <td>
                      <button className="btn btn--danger" onClick={() => handleDelete(d.id)}>
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