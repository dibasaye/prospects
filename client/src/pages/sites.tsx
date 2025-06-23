import { useState, useEffect } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useAuth } from "@/hooks/useAuth";
import { useToast } from "@/hooks/use-toast";
import { isUnauthorizedError } from "@/lib/authUtils";
import { queryClient, apiRequest } from "@/lib/queryClient";
import Header from "@/components/layout/header";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import SiteForm from "@/components/sites/site-form";
import LotGrid from "@/components/sites/lot-grid";
import LotDetails from "@/components/sites/lot-details";
import { formatCurrency } from "@/lib/constants";
import { 
  MapPin, 
  Plus, 
  Search, 
  Building2,
  BarChart3,
  Eye,
  Settings
} from "lucide-react";

export default function Sites() {
  const { toast } = useToast();
  const { isAuthenticated, isLoading } = useAuth();
  const [searchTerm, setSearchTerm] = useState("");
  const [isNewSiteOpen, setIsNewSiteOpen] = useState(false);
  const [selectedSite, setSelectedSite] = useState<any>(null);
  const [selectedLot, setSelectedLot] = useState<any>(null);
  const [isLotDetailsOpen, setIsLotDetailsOpen] = useState(false);

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      toast({
        title: "Non autorisé",
        description: "Vous êtes déconnecté. Reconnexion en cours...",
        variant: "destructive",
      });
      setTimeout(() => {
        window.location.href = "/api/login";
      }, 500);
      return;
    }
  }, [isAuthenticated, isLoading, toast]);

  const { data: sites, isLoading: sitesLoading } = useQuery({
    queryKey: ["/api/sites"],
    retry: false,
  });

  const { data: lots, isLoading: lotsLoading } = useQuery({
    queryKey: ["/api/sites", selectedSite?.id, "lots"],
    enabled: !!selectedSite,
    retry: false,
  });

  const { data: siteStats, isLoading: statsLoading } = useQuery({
    queryKey: ["/api/sites", selectedSite?.id, "stats"],
    enabled: !!selectedSite,
    retry: false,
  });

  const reserveLotMutation = useMutation({
    mutationFn: async ({ lotId, clientId, isTemporary }: { lotId: number; clientId: number; isTemporary?: boolean }) => {
      await apiRequest("POST", `/api/lots/${lotId}/reserve`, { clientId, isTemporary });
    },
    onSuccess: () => {
      toast({
        title: "Succès",
        description: "Lot réservé avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/sites"] });
    },
    onError: (error) => {
      if (isUnauthorizedError(error)) {
        toast({
          title: "Non autorisé",
          description: "Vous êtes déconnecté. Reconnexion en cours...",
          variant: "destructive",
        });
        setTimeout(() => {
          window.location.href = "/api/login";
        }, 500);
        return;
      }
      toast({
        title: "Erreur",
        description: "Impossible de réserver le lot",
        variant: "destructive",
      });
    },
  });

  if (isLoading) {
    return <div className="min-h-screen bg-gray-50 flex items-center justify-center">
      <div className="text-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
        <p>Chargement...</p>
      </div>
    </div>;
  }

  if (!isAuthenticated) {
    return null;
  }

  const handleLotClick = (lot: any) => {
    setSelectedLot(lot);
    setIsLotDetailsOpen(true);
  };

  const handleLotReserve = (lotId: number, clientId: number, isTemporary = false) => {
    reserveLotMutation.mutate({ lotId, clientId, isTemporary });
  };

  const filteredSites = sites?.filter((site: any) =>
    !searchTerm || site.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    site.location.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Gestion des Sites & Lots</h1>
            <p className="text-gray-600">Visualisation en temps réel des disponibilités</p>
          </div>
          
          <Dialog open={isNewSiteOpen} onOpenChange={setIsNewSiteOpen}>
            <DialogTrigger asChild>
              <Button className="bg-success hover:bg-success/90">
                <Plus className="w-4 h-4 mr-2" />
                Nouveau Site
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-2xl">
              <DialogHeader>
                <DialogTitle>Créer un nouveau site</DialogTitle>
                <DialogDescription>
                  Ajoutez un nouveau site immobilier avec ses caractéristiques.
                </DialogDescription>
              </DialogHeader>
              <SiteForm onSuccess={() => setIsNewSiteOpen(false)} />
            </DialogContent>
          </Dialog>
        </div>

        {/* Search */}
        <Card className="mb-6">
          <CardContent className="p-4">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
              <Input
                placeholder="Rechercher un site..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10"
              />
            </div>
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
          {/* Sites List */}
          <div className="lg:col-span-1">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center">
                  <Building2 className="w-5 h-5 mr-2" />
                  Sites Disponibles
                </CardTitle>
              </CardHeader>
              <CardContent className="p-0">
                <div className="space-y-2 p-4">
                  {sitesLoading ? (
                    Array.from({ length: 3 }).map((_, i) => (
                      <div key={i} className="p-3 rounded-lg border">
                        <Skeleton className="h-4 w-24 mb-2" />
                        <Skeleton className="h-3 w-16 mb-1" />
                        <Skeleton className="h-3 w-20" />
                      </div>
                    ))
                  ) : filteredSites && filteredSites.length > 0 ? (
                    filteredSites.map((site: any) => (
                      <div
                        key={site.id}
                        className={`p-3 rounded-lg border cursor-pointer transition-colors hover:bg-gray-50 ${
                          selectedSite?.id === site.id ? 'border-primary bg-primary/5' : 'border-gray-200'
                        }`}
                        onClick={() => setSelectedSite(site)}
                      >
                        <div className="flex items-center justify-between mb-2">
                          <h4 className="font-medium text-gray-900">{site.name}</h4>
                          {site.isActive ? (
                            <Badge className="bg-success/10 text-success">Actif</Badge>
                          ) : (
                            <Badge variant="secondary">Inactif</Badge>
                          )}
                        </div>
                        <p className="text-sm text-gray-500">{site.location}</p>
                        <p className="text-sm font-medium text-gray-900 mt-1">
                          Adhésion: {formatCurrency(site.adhesionFee)}
                        </p>
                      </div>
                    ))
                  ) : (
                    <div className="text-center py-8">
                      <Building2 className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                      <p className="text-gray-500">Aucun site trouvé</p>
                    </div>
                  )}
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Site Details & Lots */}
          <div className="lg:col-span-3">
            {selectedSite ? (
              <div className="space-y-6">
                {/* Site Header */}
                <Card>
                  <CardHeader>
                    <div className="flex items-center justify-between">
                      <div>
                        <div className="flex items-center space-x-4 mb-2">
                          <h3 className="text-lg font-semibold text-gray-900">{selectedSite.name}</h3>
                          <Badge className={selectedSite.isActive ? "bg-success/10 text-success" : "bg-gray-100 text-gray-600"}>
                            {selectedSite.isActive ? "Actif" : "Inactif"}
                          </Badge>
                        </div>
                        <p className="text-gray-600">{selectedSite.location}</p>
                        {selectedSite.department && (
                          <p className="text-sm text-gray-500">{selectedSite.department}, {selectedSite.commune}</p>
                        )}
                      </div>
                      <div className="flex space-x-2">
                        <Button variant="outline" size="sm">
                          <Eye className="w-4 h-4 mr-2" />
                          Voir Plan
                        </Button>
                        <Button variant="outline" size="sm">
                          <Settings className="w-4 h-4 mr-2" />
                          Modifier
                        </Button>
                      </div>
                    </div>
                  </CardHeader>
                </Card>

                {/* Site Stats */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <Card>
                    <CardContent className="p-4 text-center">
                      {statsLoading ? (
                        <Skeleton className="h-8 w-16 mx-auto mb-2" />
                      ) : (
                        <p className="text-2xl font-bold text-gray-900">{siteStats?.total || 0}</p>
                      )}
                      <p className="text-sm text-gray-500">Total Lots</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardContent className="p-4 text-center bg-success/5">
                      {statsLoading ? (
                        <Skeleton className="h-8 w-16 mx-auto mb-2" />
                      ) : (
                        <p className="text-2xl font-bold text-success">{siteStats?.available || 0}</p>
                      )}
                      <p className="text-sm text-success">Disponibles</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardContent className="p-4 text-center bg-warning/5">
                      {statsLoading ? (
                        <Skeleton className="h-8 w-16 mx-auto mb-2" />
                      ) : (
                        <p className="text-2xl font-bold text-warning">{siteStats?.reserved || 0}</p>
                      )}
                      <p className="text-sm text-warning">Réservés</p>
                    </CardContent>
                  </Card>
                  <Card>
                    <CardContent className="p-4 text-center bg-gray-50">
                      {statsLoading ? (
                        <Skeleton className="h-8 w-16 mx-auto mb-2" />
                      ) : (
                        <p className="text-2xl font-bold text-gray-600">{siteStats?.sold || 0}</p>
                      )}
                      <p className="text-sm text-gray-600">Vendus</p>
                    </CardContent>
                  </Card>
                </div>

                {/* Lot Grid */}
                <Card>
                  <CardHeader>
                    <div className="flex items-center justify-between">
                      <CardTitle>Plan du Site</CardTitle>
                      <div className="flex items-center space-x-4">
                        <div className="flex items-center space-x-2">
                          <div className="w-3 h-3 bg-success rounded-full"></div>
                          <span className="text-xs text-gray-600">Disponible</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <div className="w-3 h-3 bg-warning rounded-full"></div>
                          <span className="text-xs text-gray-600">Réservé</span>
                        </div>
                        <div className="flex items-center space-x-2">
                          <div className="w-3 h-3 bg-gray-500 rounded-full"></div>
                          <span className="text-xs text-gray-600">Vendu</span>
                        </div>
                      </div>
                    </div>
                  </CardHeader>
                  <CardContent>
                    {lotsLoading ? (
                      <div className="grid grid-cols-10 gap-2">
                        {Array.from({ length: 40 }).map((_, i) => (
                          <Skeleton key={i} className="aspect-square" />
                        ))}
                      </div>
                    ) : lots ? (
                      <LotGrid 
                        lots={lots} 
                        onLotClick={handleLotClick}
                        onLotReserve={handleLotReserve}
                      />
                    ) : (
                      <div className="text-center py-8">
                        <MapPin className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <p className="text-gray-500">Aucun lot configuré pour ce site</p>
                        <Button className="mt-4" variant="outline">
                          <Plus className="w-4 h-4 mr-2" />
                          Ajouter des lots
                        </Button>
                      </div>
                    )}
                  </CardContent>
                </Card>
              </div>
            ) : (
              <Card>
                <CardContent className="p-12 text-center">
                  <Building2 className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                  <h3 className="text-lg font-medium text-gray-900 mb-2">Sélectionnez un site</h3>
                  <p className="text-gray-500 mb-6">
                    Choisissez un site dans la liste pour voir ses détails et gérer ses lots
                  </p>
                  <Button onClick={() => setIsNewSiteOpen(true)} className="bg-success hover:bg-success/90">
                    <Plus className="w-4 h-4 mr-2" />
                    Créer un nouveau site
                  </Button>
                </CardContent>
              </Card>
            )}
          </div>
        </div>

        {/* Lot Details Modal */}
        <Dialog open={isLotDetailsOpen} onOpenChange={setIsLotDetailsOpen}>
          <DialogContent className="max-w-2xl">
            <DialogHeader>
              <DialogTitle>Détails du Lot {selectedLot?.lotNumber}</DialogTitle>
              <DialogDescription>
                Informations complètes et actions disponibles pour ce lot.
              </DialogDescription>
            </DialogHeader>
            {selectedLot && (
              <LotDetails 
                lot={selectedLot} 
                site={selectedSite}
                onReserve={handleLotReserve}
                onClose={() => setIsLotDetailsOpen(false)}
              />
            )}
          </DialogContent>
        </Dialog>
      </div>
    </div>
  );
}
