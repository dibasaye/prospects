import { useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { useAuth } from "@/hooks/useAuth";
import { useToast } from "@/hooks/use-toast";
import { isUnauthorizedError } from "@/lib/authUtils";
import Header from "@/components/layout/header";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";
import { formatCurrency, formatDateTime, PROSPECT_STATUS, PROSPECT_STATUS_LABELS } from "@/lib/constants";
import { 
  Users, 
  Handshake, 
  MapPin, 
  Coins,
  UserPlus,
  Phone,
  MessageCircle,
  Eye,
  Plus,
  Building,
  FileText,
  CreditCard,
  BarChart,
  CheckCircle,
  AlertCircle,
  Clock
} from "lucide-react";

export default function Dashboard() {
  const { toast } = useToast();
  const { isAuthenticated, isLoading } = useAuth();

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

  const { data: stats, isLoading: statsLoading } = useQuery({
    queryKey: ["/api/dashboard/stats"],
    retry: false,
  });

  const { data: activities, isLoading: activitiesLoading } = useQuery({
    queryKey: ["/api/dashboard/activities"],
    retry: false,
  });

  const { data: prospects, isLoading: prospectsLoading } = useQuery({
    queryKey: ["/api/prospects", { status: PROSPECT_STATUS.NOUVEAU, limit: 10 }],
    retry: false,
  });

  const { data: sites, isLoading: sitesLoading } = useQuery({
    queryKey: ["/api/sites", { isActive: true }],
    retry: false,
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

  const getInitials = (firstName: string, lastName: string) => {
    return `${firstName[0]}${lastName[0]}`.toUpperCase();
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case PROSPECT_STATUS.NOUVEAU:
        return "bg-blue-100 text-blue-800";
      case PROSPECT_STATUS.EN_RELANCE:
        return "bg-yellow-100 text-yellow-800";
      case PROSPECT_STATUS.INTERESSE:
        return "bg-green-100 text-green-800";
      case PROSPECT_STATUS.CONVERTI:
        return "bg-purple-100 text-purple-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {/* Dashboard Overview */}
        <div className="mb-8">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">Tableau de Bord</h2>
          
          {/* Stats Overview */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card>
              <CardContent className="p-6">
                <div className="flex items-center">
                  <div className="flex-shrink-0">
                    <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                      <UserPlus className="text-primary w-6 h-6" />
                    </div>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-500">Nouveaux Prospects</p>
                    {statsLoading ? (
                      <Skeleton className="h-8 w-16 mt-1" />
                    ) : (
                      <p className="text-2xl font-bold text-gray-900">{stats?.newProspects || 0}</p>
                    )}
                    <p className="text-xs text-success">+12% ce mois</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="p-6">
                <div className="flex items-center">
                  <div className="flex-shrink-0">
                    <div className="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                      <Handshake className="text-success w-6 h-6" />
                    </div>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-500">Conversions</p>
                    {statsLoading ? (
                      <Skeleton className="h-8 w-16 mt-1" />
                    ) : (
                      <p className="text-2xl font-bold text-gray-900">{stats?.conversions || 0}</p>
                    )}
                    <p className="text-xs text-success">+25% ce mois</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="p-6">
                <div className="flex items-center">
                  <div className="flex-shrink-0">
                    <div className="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                      <MapPin className="text-warning w-6 h-6" />
                    </div>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-500">Lots Disponibles</p>
                    {statsLoading ? (
                      <Skeleton className="h-8 w-16 mt-1" />
                    ) : (
                      <p className="text-2xl font-bold text-gray-900">{stats?.availableLots || 0}</p>
                    )}
                    <p className="text-xs text-gray-500">sur {stats?.totalLots || 0} total</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="p-6">
                <div className="flex items-center">
                  <div className="flex-shrink-0">
                    <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                      <Coins className="text-primary w-6 h-6" />
                    </div>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-500">CA ce mois</p>
                    {statsLoading ? (
                      <Skeleton className="h-8 w-20 mt-1" />
                    ) : (
                      <p className="text-2xl font-bold text-gray-900">
                        {stats?.totalRevenue ? formatCurrency(stats.totalRevenue) : '0 FCFA'}
                      </p>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Prospects Pipeline */}
          <div className="lg:col-span-2">
            <Card>
              <div className="p-6 border-b border-gray-200">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-gray-900">Pipeline des Prospects</h3>
                  <Button>
                    <Plus className="w-4 h-4 mr-2" />
                    Nouveau Prospect
                  </Button>
                </div>
              </div>
              
              <div className="p-6">
                <Tabs defaultValue="nouveau" className="w-full">
                  <TabsList className="grid w-full grid-cols-4">
                    <TabsTrigger value="nouveau">Nouveaux</TabsTrigger>
                    <TabsTrigger value="en_relance">En relance</TabsTrigger>
                    <TabsTrigger value="interesse">Intéressés</TabsTrigger>
                    <TabsTrigger value="converti">Convertis</TabsTrigger>
                  </TabsList>
                  
                  <TabsContent value="nouveau" className="mt-6">
                    <div className="space-y-4">
                      {prospectsLoading ? (
                        Array.from({ length: 3 }).map((_, i) => (
                          <Card key={i}>
                            <CardContent className="p-4">
                              <div className="flex items-center space-x-4">
                                <Skeleton className="w-10 h-10 rounded-full" />
                                <div className="flex-1">
                                  <Skeleton className="h-4 w-32 mb-2" />
                                  <Skeleton className="h-3 w-24" />
                                </div>
                              </div>
                            </CardContent>
                          </Card>
                        ))
                      ) : prospects && prospects.length > 0 ? (
                        prospects.map((prospect: any) => (
                          <Card key={prospect.id} className="hover:shadow-sm transition-shadow">
                            <CardContent className="p-4">
                              <div className="flex items-center justify-between">
                                <div className="flex items-center space-x-4">
                                  <Avatar>
                                    <AvatarFallback className="bg-primary/10 text-primary">
                                      {getInitials(prospect.firstName, prospect.lastName)}
                                    </AvatarFallback>
                                  </Avatar>
                                  <div>
                                    <h4 className="font-medium text-gray-900">
                                      {prospect.firstName} {prospect.lastName}
                                    </h4>
                                    <p className="text-sm text-gray-500">{prospect.phone}</p>
                                    {prospect.interestedSiteId && (
                                      <p className="text-xs text-gray-400">
                                        Intéressé par le site #{prospect.interestedSiteId}
                                      </p>
                                    )}
                                  </div>
                                </div>
                                <div className="flex items-center space-x-2">
                                  <Badge className={getStatusColor(prospect.status)}>
                                    {PROSPECT_STATUS_LABELS[prospect.status as keyof typeof PROSPECT_STATUS_LABELS]}
                                  </Badge>
                                  <div className="flex space-x-1">
                                    <Button variant="ghost" size="sm">
                                      <Phone className="w-4 h-4" />
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                      <MessageCircle className="w-4 h-4" />
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                      <Eye className="w-4 h-4" />
                                    </Button>
                                  </div>
                                </div>
                              </div>
                              <div className="mt-3 flex items-center justify-between">
                                <p className="text-xs text-gray-500">
                                  Créé {formatDateTime(prospect.createdAt)}
                                  {prospect.assignedToId ? ` • Assigné` : ` • Non assigné`}
                                </p>
                                {!prospect.assignedToId && (
                                  <Button variant="outline" size="sm">
                                    Assigner à un commercial
                                  </Button>
                                )}
                              </div>
                            </CardContent>
                          </Card>
                        ))
                      ) : (
                        <div className="text-center py-8">
                          <Users className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                          <p className="text-gray-500">Aucun nouveau prospect</p>
                        </div>
                      )}
                    </div>
                  </TabsContent>
                </Tabs>
              </div>
            </Card>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Quick Actions */}
            <Card>
              <div className="p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                <div className="space-y-3">
                  <Button variant="outline" className="w-full justify-between">
                    <div className="flex items-center">
                      <Building className="w-4 h-4 mr-3 text-primary" />
                      <span>Créer un Site</span>
                    </div>
                    <i className="fas fa-chevron-right text-gray-400 text-xs" />
                  </Button>
                  
                  <Button variant="outline" className="w-full justify-between">
                    <div className="flex items-center">
                      <FileText className="w-4 h-4 mr-3 text-success" />
                      <span>Générer Contrat</span>
                    </div>
                    <i className="fas fa-chevron-right text-gray-400 text-xs" />
                  </Button>
                  
                  <Button variant="outline" className="w-full justify-between">
                    <div className="flex items-center">
                      <CreditCard className="w-4 h-4 mr-3 text-warning" />
                      <span>Enregistrer Paiement</span>
                    </div>
                    <i className="fas fa-chevron-right text-gray-400 text-xs" />
                  </Button>
                  
                  <Button variant="outline" className="w-full justify-between">
                    <div className="flex items-center">
                      <BarChart className="w-4 h-4 mr-3 text-purple-600" />
                      <span>Rapports</span>
                    </div>
                    <i className="fas fa-chevron-right text-gray-400 text-xs" />
                  </Button>
                </div>
              </div>
            </Card>

            {/* Sites Overview */}
            <Card>
              <div className="p-6">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="text-lg font-semibold text-gray-900">Sites Actifs</h3>
                  <Button variant="ghost" size="sm">
                    Voir tout
                  </Button>
                </div>
                
                <div className="space-y-4">
                  {sitesLoading ? (
                    Array.from({ length: 2 }).map((_, i) => (
                      <div key={i} className="p-3 rounded-lg border border-gray-200">
                        <Skeleton className="h-4 w-24 mb-2" />
                        <div className="flex items-center space-x-4">
                          <Skeleton className="h-3 w-16" />
                          <Skeleton className="h-3 w-16" />
                          <Skeleton className="h-3 w-16" />
                        </div>
                      </div>
                    ))
                  ) : sites && sites.length > 0 ? (
                    sites.slice(0, 2).map((site: any) => (
                      <div key={site.id} className="p-3 rounded-lg border border-gray-200">
                        <div className="flex items-center justify-between">
                          <div>
                            <h4 className="font-medium text-gray-900">{site.name}</h4>
                            <div className="flex items-center space-x-4 mt-1">
                              <span className="text-xs text-success font-medium">
                                {site.availableLots || 0} disponibles
                              </span>
                              <span className="text-xs text-warning">
                                {site.reservedLots || 0} réservés
                              </span>
                              <span className="text-xs text-gray-500">
                                {site.soldLots || 0} vendus
                              </span>
                            </div>
                          </div>
                          <div className="text-right">
                            <p className="text-sm font-medium text-gray-900">
                              {formatCurrency(site.adhesionFee)}
                            </p>
                            <p className="text-xs text-gray-500">Adhésion</p>
                          </div>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="text-center py-4">
                      <Building className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                      <p className="text-sm text-gray-500">Aucun site actif</p>
                    </div>
                  )}
                </div>
              </div>
            </Card>

            {/* Recent Notifications */}
            <Card>
              <div className="p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Notifications Récentes</h3>
                <div className="space-y-3">
                  {activitiesLoading ? (
                    Array.from({ length: 3 }).map((_, i) => (
                      <div key={i} className="flex items-start space-x-3 p-2 rounded-lg">
                        <Skeleton className="w-2 h-2 rounded-full mt-2" />
                        <div className="flex-1">
                          <Skeleton className="h-3 w-32 mb-1" />
                          <Skeleton className="h-2 w-16" />
                        </div>
                      </div>
                    ))
                  ) : activities && activities.length > 0 ? (
                    activities.slice(0, 3).map((activity: any) => (
                      <div key={activity.id} className="flex items-start space-x-3 p-2 rounded-lg hover:bg-gray-50">
                        <div className="w-2 h-2 bg-primary rounded-full mt-2 flex-shrink-0" />
                        <div>
                          <p className="text-sm text-gray-900">{activity.description}</p>
                          <p className="text-xs text-gray-500">{formatDateTime(activity.createdAt)}</p>
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="text-center py-4">
                      <AlertCircle className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                      <p className="text-sm text-gray-500">Aucune activité récente</p>
                    </div>
                  )}
                </div>
              </div>
            </Card>
          </div>
        </div>

        {/* Recent Activities */}
        <div className="mt-8">
          <Card>
            <div className="p-6 border-b border-gray-200">
              <h3 className="text-lg font-semibold text-gray-900">Activités Récentes</h3>
            </div>
            <div className="p-6">
              <div className="flow-root">
                <ul className="-mb-8">
                  {activitiesLoading ? (
                    Array.from({ length: 3 }).map((_, i) => (
                      <li key={i}>
                        <div className="relative pb-8">
                          <span className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" />
                          <div className="relative flex space-x-3">
                            <Skeleton className="h-8 w-8 rounded-full" />
                            <div className="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                              <div className="flex-1">
                                <Skeleton className="h-4 w-64 mb-1" />
                                <Skeleton className="h-3 w-32" />
                              </div>
                              <Skeleton className="h-3 w-16" />
                            </div>
                          </div>
                        </div>
                      </li>
                    ))
                  ) : activities && activities.length > 0 ? (
                    activities.map((activity: any, index: number) => (
                      <li key={activity.id}>
                        <div className="relative pb-8">
                          {index < activities.length - 1 && (
                            <span className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" />
                          )}
                          <div className="relative flex space-x-3">
                            <div>
                              <span className="h-8 w-8 rounded-full bg-success flex items-center justify-center ring-8 ring-white">
                                <CheckCircle className="w-4 h-4 text-white" />
                              </span>
                            </div>
                            <div className="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                              <div>
                                <p className="text-sm text-gray-900">{activity.description}</p>
                                <p className="text-xs text-gray-400">
                                  {activity.entityType} #{activity.entityId}
                                </p>
                              </div>
                              <div className="text-right text-sm whitespace-nowrap text-gray-500">
                                <time>{formatDateTime(activity.createdAt)}</time>
                              </div>
                            </div>
                          </div>
                        </div>
                      </li>
                    ))
                  ) : (
                    <li className="text-center py-8">
                      <Clock className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                      <p className="text-gray-500">Aucune activité récente</p>
                    </li>
                  )}
                </ul>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
}
