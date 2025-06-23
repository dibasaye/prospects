import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Building2, Users, MapPin, FileText } from "lucide-react";

export default function Landing() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-primary/10 to-background">
      <div className="container mx-auto px-4 py-16">
        <div className="text-center mb-16">
          <h1 className="text-4xl font-bold text-foreground mb-4">
            YAYE DIA BTP
          </h1>
          <p className="text-xl text-muted-foreground mb-8">
            Système de Gestion Immobilière Intégré
          </p>
          <p className="text-lg text-muted-foreground mb-8 max-w-2xl mx-auto">
            Gérez efficacement vos prospects, sites, lots et contrats immobiliers avec notre plateforme complète.
          </p>
          <Button 
            size="lg" 
            onClick={() => window.location.href = '/api/login'}
            className="bg-primary hover:bg-primary/90"
          >
            Se connecter
          </Button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          <Card>
            <CardHeader className="text-center">
              <Users className="w-12 h-12 text-primary mx-auto mb-4" />
              <CardTitle>Gestion des Prospects</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground text-center">
                Suivez le cycle complet de vos prospects depuis le premier contact jusqu'à la conversion.
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="text-center">
              <MapPin className="w-12 h-12 text-primary mx-auto mb-4" />
              <CardTitle>Sites & Lots</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground text-center">
                Visualisez et gérez vos sites immobiliers avec une interface interactive.
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="text-center">
              <FileText className="w-12 h-12 text-primary mx-auto mb-4" />
              <CardTitle>Contrats & Paiements</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground text-center">
                Générez automatiquement les contrats et suivez les paiements.
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="text-center">
              <Building2 className="w-12 h-12 text-primary mx-auto mb-4" />
              <CardTitle>Suivi Commercial</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground text-center">
                Tableaux de bord et rapports pour optimiser votre performance commerciale.
              </p>
            </CardContent>
          </Card>
        </div>

        <div className="text-center">
          <h2 className="text-2xl font-semibold mb-4">Prêt à commencer ?</h2>
          <p className="text-muted-foreground mb-6">
            Connectez-vous pour accéder à votre espace de gestion immobilière.
          </p>
          <Button 
            onClick={() => window.location.href = '/api/login'}
            className="bg-primary hover:bg-primary/90"
          >
            Accéder à l'application
          </Button>
        </div>
      </div>
    </div>
  );
}
