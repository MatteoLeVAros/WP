import { BrowserRouter, Routes, Route } from "react-router-dom";
import LoginPage from "../pages/LoginPage";
import RegisterPage from "../pages/RegisterPage";
import ProfilePage from "../pages/ProfilePage";
import ProtectedRoute from "../components/ProtectedRoute";
import TachesPage from "../pages/TachesPage";
import CampagnesPage from "../pages/CampagnesPage";
import CampagneDetailPage from "../pages/CampagneDetailPage";
import TacheDetailPage from "../pages/TacheDetailPage";
import Layout from "../components/Layout";
import DashboardPage from "../pages/DashboardPage";
import PlanningPage from "../pages/PlanningPage";
import DemandesInterventionPage from "../pages/DemandesInterventionPage";
import RequireAdmin from "../components/RequireAdmin";

export default function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />

        <Route
          path="/taches"
          element={
            <ProtectedRoute>
              <Layout>
                <TachesPage />
              </Layout>
            </ProtectedRoute>
          }
        />
        <Route
          path="/campagnes"
          element={
            <ProtectedRoute>
              <RequireAdmin>
                <Layout>
                  <CampagnesPage />
                </Layout>
              </RequireAdmin>
            </ProtectedRoute>
          }
        />
        
        <Route
          path="/campagnes/:id"
          element={
            <ProtectedRoute>
                <Layout>
                  <CampagneDetailPage />
                </Layout>
            </ProtectedRoute>
          }
        />
        
        <Route
          path="/taches/:id"
          element={
            <ProtectedRoute>
              <Layout>
                <TacheDetailPage />
              </Layout>
            </ProtectedRoute>
          }
        />
        
        <Route
          path="/profile"
          element={
            <ProtectedRoute>
              <Layout>
                <ProfilePage />
              </Layout>
            </ProtectedRoute>
          }
        />
        
        <Route
          path="/"
          element={
            <ProtectedRoute>
              <Layout>
                <DashboardPage />
              </Layout>
            </ProtectedRoute>
          }
        />
        
        <Route
          path="/planning"
          element={
            <ProtectedRoute>
              <Layout>
                <PlanningPage />
              </Layout>
            </ProtectedRoute>
          }
        />
        
        <Route
          path="/demandes-intervention"
          element={
            <ProtectedRoute>
              <Layout>
                <DemandesInterventionPage />
              </Layout>
            </ProtectedRoute>
          }
        />





      </Routes>
    </BrowserRouter>
  );
}