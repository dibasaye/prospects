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
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import ProspectForm from "@/components/prospects/prospect-form";
import { 
  formatDateTime, 
  PROSPECT_STATUS, 
  PROSPECT_STATUS_LABELS 
} from "@/lib/constants";
import { 
  Users, 
  Plus, 
  Search, 
  Phone, 
  MessageCircle, 
  Eye, 
  UserPlus,
  Filter
} from "lucide-react";

export default function Prospects() {
  const { toast } = useToast();
  const { isAuthenticated, isLoading } = useAuth();
  const [activeTab, setActiveTab] = useState(PROSPECT_STATUS.NOUVEAU);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedSite, setSelectedSite] = useState<string>("");
  const [isNewProspectOpen, setIsNewProspectOpen] = useState(false);

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

  const { data: prospects, isLoading: prospectsLoading } = useQuery({
    queryKey: ["/api/prospects", { status: activeTab, limit: 50 }],
    retry: false,
  });

  const { data: sites } = useQuery({
    queryKey: ["/api/sites", { isActive: true }],
    retry: false,
  });

  const assignProspectMutation = useMutation({
    mutationFn: async ({ prospectId, commercialId }: { prospectId: number; commercialId: string }) => {
      await apiRequest("POST", `/api/prospects/${prospectId}/assign`, { commercialId });
    },
    onSuccess: () => {
      toast({
        title: "Succès",
        description: "Prospect assigné avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/prospects"] });
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
        description: "Impossible d'assigner le prospect",
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

  const getProspectCounts = () => {
    // This would normally come from the API
    return {
      [PROSPECT_STATUS.NOUVEAU]: prospects?.filter((p: any) => p.status === PROSPECT_STATUS.NOUVEAU).length || 0,
      [PROSPECT_STATUS.EN_RELANCE]: 0,
      [PROSPECT_STATUS.INTERESSE]: 0,
      [PROSPECT_STATUS.CONVERTI]: 0,
    };
  };

  const counts = getProspectCounts();

  const handleAssignProspect = async (prospectId: number, commercialId: string) => {
    assignProspectMutation.mutate({ prospectId, commercialId });
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Gestion des Prospects</h1>
            <p className="text-gray-600">Suivez et gérez vos prospects du premier contact à la conversion</p>
          </div>
          
          <Dialog open={isNewProspectOpen} onOpenChange={setIsNewProspectOpen}>
            <DialogTrigger asChild>
              <Button>
                <Plus className="w-4 h-4 mr-2" />
                Nouveau Prospect
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-2xl">
              <DialogHeader>
                <DialogTitle>Ajouter un nouveau prospect</DialogTitle>
                <DialogDescription>
                  Saisissez les informations du prospect pour l'ajouter au système.
                </DialogDescription>
              </DialogHeader>
              <ProspectForm onSuccess={() => setIsNewProspectOpen(false)} />
            </DialogContent>
          </Dialog>
        </div>

        {/* Filters */}
        <Card className="mb-6">
          <CardContent className="p-4">
            <div className="flex flex-col sm:flex-row gap-4">
              <div className="flex-1">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                  <Input
                    placeholder="Rechercher un prospect..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10"
                  />
                </div>
              </div>
              <div className="flex gap-2">
                <Select value={selectedSite} onValueChange={setSelectedSite}>
                  <SelectTrigger className="w-[200px]">
                    <SelectValue placeholder="Filtrer par site" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">Tous les sites</SelectItem>
                    {sites?.map((site: any) => (
                      <SelectItem key={site.id} value={site.id.toString()}>
                        {site.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <Button variant="outline" size="icon">
                  <Filter className="w-4 h-4" />
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Prospects Pipeline */}
        <Card>
          <CardHeader>
            <CardTitle>Pipeline des Prospects</CardTitle>
          </CardHeader>
          <CardContent>
            <Tabs value={activeTab} onValueChange={setActiveTab}>
              <TabsList className="grid w-full grid-cols-4">
                <TabsTrigger value={PROSPECT_STATUS.NOUVEAU}>
                  Nouveaux ({counts[PROSPECT_STATUS.NOUVEAU]})
                </TabsTrigger>
                <TabsTrigger value={PROSPECT_STATUS.EN_RELANCE}>
                  En relance ({counts[PROSPECT_STATUS.EN_RELANCE]})
                </TabsTrigger>
                <TabsTrigger value={PROSPECT_STATUS.INTERESSE}>
                  Intéressés ({counts[PROSPECT_STATUS.INTERESSE]})
                </TabsTrigger>
                <TabsTrigger value={PROSPECT_STATUS.CONVERTI}>
                  Convertis ({counts[PROSPECT_STATUS.CONVERTI]})
                </TabsTrigger>
              </TabsList>

              <TabsContent value={activeTab} className="mt-6">
                <div className="space-y-4">
                  {prospectsLoading ? (
                    Array.from({ length: 5 }).map((_, i) => (
                      <Card key={i}>
                        <CardContent className="p-4">
                          <div className="flex items-center space-x-4">
                            <Skeleton className="w-12 h-12 rounded-full" />
                            <div className="flex-1">
                              <Skeleton className="h-4 w-32 mb-2" />
                              <Skeleton className="h-3 w-24 mb-1" />
                              <Skeleton className="h-3 w-40" />
                            </div>
                            <div className="flex space-x-2">
                              <Skeleton className="h-6 w-16" />
                              <Skeleton className="h-8 w-8" />
                              <Skeleton className="h-8 w-8" />
                              <Skeleton className="h-8 w-8" />
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    ))
                  ) : prospects && prospects.length > 0 ? (
                    prospects
                      .filter((prospect: any) => {
                        const matchesSearch = !searchTerm || 
                          `${prospect.firstName} ${prospect.lastName}`.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          prospect.phone.includes(searchTerm);
                        const matchesSite = !selectedSite || prospect.interestedSiteId?.toString() === selectedSite;
                        return matchesSearch && matchesSite;
                      })
                      .map((prospect: any) => (
                        <Card key={prospect.id} className="hover:shadow-sm transition-shadow">
                          <CardContent className="p-4">
                            <div className="flex items-center justify-between">
                              <div className="flex items-center space-x-4">
                                <Avatar className="w-12 h-12">
                                  <AvatarFallback className="bg-primary/10 text-primary">
                                    {getInitials(prospect.firstName, prospect.lastName)}
                                  </AvatarFallback>
                                </Avatar>
                                <div>
                                  <h4 className="font-medium text-gray-900">
                                    {prospect.firstName} {prospect.lastName}
                                  </h4>
                                  <p className="text-sm text-gray-500">{prospect.phone}</p>
                                  {prospect.email && (
                                    <p className="text-sm text-gray-500">{prospect.email}</p>
                                  )}
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
                                  <Button variant="ghost" size="sm" title="Appeler">
                                    <Phone className="w-4 h-4" />
                                  </Button>
                                  <Button variant="ghost" size="sm" title="WhatsApp">
                                    <MessageCircle className="w-4 h-4" />
                                  </Button>
                                  <Button variant="ghost" size="sm" title="Voir détails">
                                    <Eye className="w-4 h-4" />
                                  </Button>
                                  {!prospect.assignedToId && (
                                    <Button 
                                      variant="ghost" 
                                      size="sm" 
                                      title="Assigner"
                                      onClick={() => handleAssignProspect(prospect.id, "current-user-id")}
                                      disabled={assignProspectMutation.isPending}
                                    >
                                      <UserPlus className="w-4 h-4" />
                                    </Button>
                                  )}
                                </div>
                              </div>
                            </div>
                            <div className="mt-3 flex items-center justify-between">
                              <p className="text-xs text-gray-500">
                                Créé {formatDateTime(prospect.createdAt)}
                                {prospect.assignedToId ? ` • Assigné` : ` • Non assigné`}
                                {prospect.lastContactDate && ` • Dernier contact: ${formatDateTime(prospect.lastContactDate)}`}
                              </p>
                              {!prospect.assignedToId && (
                                <Button 
                                  variant="outline" 
                                  size="sm"
                                  onClick={() => handleAssignProspect(prospect.id, "current-user-id")}
                                  disabled={assignProspectMutation.isPending}
                                >
                                  Assigner à un commercial
                                </Button>
                              )}
                            </div>
                          </CardContent>
                        </Card>
                      ))
                  ) : (
                    <div className="text-center py-12">
                      <Users className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                      <h3 className="text-lg font-medium text-gray-900 mb-2">
                        Aucun prospect {PROSPECT_STATUS_LABELS[activeTab as keyof typeof PROSPECT_STATUS_LABELS].toLowerCase()}
                      </h3>
                      <p className="text-gray-500 mb-6">
                        {activeTab === PROSPECT_STATUS.NOUVEAU 
                          ? "Les nouveaux prospects apparaîtront ici"
                          : "Aucun prospect dans cette catégorie pour le moment"
                        }
                      </p>
                      {activeTab === PROSPECT_STATUS.NOUVEAU && (
                        <Button onClick={() => setIsNewProspectOpen(true)}>
                          <Plus className="w-4 h-4 mr-2" />
                          Ajouter un prospect
                        </Button>
                      )}
                    </div>
                  )}
                </div>
              </TabsContent>
            </Tabs>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
