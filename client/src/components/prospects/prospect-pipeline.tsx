import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useToast } from "@/hooks/use-toast";
import { isUnauthorizedError } from "@/lib/authUtils";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { 
  formatDateTime, 
  PROSPECT_STATUS, 
  PROSPECT_STATUS_LABELS 
} from "@/lib/constants";
import { 
  Phone, 
  MessageCircle, 
  Eye, 
  UserPlus,
  Calendar,
  Clock
} from "lucide-react";

interface ProspectPipelineProps {
  status: string;
  searchTerm?: string;
  siteFilter?: string;
}

export default function ProspectPipeline({ status, searchTerm = "", siteFilter = "" }: ProspectPipelineProps) {
  const { toast } = useToast();
  const [selectedCommercial, setSelectedCommercial] = useState<string>("");

  const { data: prospects, isLoading: prospectsLoading } = useQuery({
    queryKey: ["/api/prospects", { status, limit: 50 }],
    retry: false,
  });

  const { data: commercials } = useQuery({
    queryKey: ["/api/users", { role: "commercial" }],
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

  const updateProspectMutation = useMutation({
    mutationFn: async ({ prospectId, updates }: { prospectId: number; updates: any }) => {
      await apiRequest("PUT", `/api/prospects/${prospectId}`, updates);
    },
    onSuccess: () => {
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
        description: "Impossible de modifier le prospect",
        variant: "destructive",
      });
    },
  });

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

  const handleAssignProspect = (prospectId: number) => {
    if (!selectedCommercial) {
      toast({
        title: "Erreur",
        description: "Veuillez sélectionner un commercial",
        variant: "destructive",
      });
      return;
    }
    assignProspectMutation.mutate({ prospectId, commercialId: selectedCommercial });
  };

  const handleStatusChange = (prospectId: number, newStatus: string) => {
    updateProspectMutation.mutate({
      prospectId,
      updates: { status: newStatus, lastContactDate: new Date().toISOString() }
    });
  };

  const filteredProspects = prospects?.filter((prospect: any) => {
    const matchesSearch = !searchTerm || 
      `${prospect.firstName} ${prospect.lastName}`.toLowerCase().includes(searchTerm.toLowerCase()) ||
      prospect.phone.includes(searchTerm);
    const matchesSite = !siteFilter || prospect.interestedSiteId?.toString() === siteFilter;
    return matchesSearch && matchesSite;
  });

  if (prospectsLoading) {
    return (
      <div className="space-y-4">
        {Array.from({ length: 5 }).map((_, i) => (
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
        ))}
      </div>
    );
  }

  if (!filteredProspects || filteredProspects.length === 0) {
    return (
      <div className="text-center py-12">
        <Clock className="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 className="text-lg font-medium text-gray-900 mb-2">
          Aucun prospect {PROSPECT_STATUS_LABELS[status as keyof typeof PROSPECT_STATUS_LABELS]?.toLowerCase()}
        </h3>
        <p className="text-gray-500">
          {status === PROSPECT_STATUS.NOUVEAU 
            ? "Les nouveaux prospects apparaîtront ici"
            : "Aucun prospect dans cette catégorie pour le moment"
          }
        </p>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {/* Assignment controls for new prospects */}
      {status === PROSPECT_STATUS.NOUVEAU && (
        <Card className="bg-blue-50 border-blue-200">
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <h4 className="font-medium text-blue-900">Assignment rapide</h4>
                <p className="text-sm text-blue-700">Sélectionnez un commercial pour assigner les prospects</p>
              </div>
              <div className="flex items-center space-x-2">
                <Select value={selectedCommercial} onValueChange={setSelectedCommercial}>
                  <SelectTrigger className="w-48">
                    <SelectValue placeholder="Choisir un commercial" />
                  </SelectTrigger>
                  <SelectContent>
                    {commercials?.map((commercial: any) => (
                      <SelectItem key={commercial.id} value={commercial.id || ""}>
                        {commercial.firstName} {commercial.lastName}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {filteredProspects.map((prospect: any) => (
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
                  {prospect.notes && (
                    <p className="text-xs text-gray-600 mt-1 max-w-md truncate">
                      {prospect.notes}
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
                  {status === PROSPECT_STATUS.NOUVEAU && !prospect.assignedToId && (
                    <Button 
                      variant="ghost" 
                      size="sm" 
                      title="Assigner"
                      onClick={() => handleAssignProspect(prospect.id)}
                      disabled={assignProspectMutation.isPending || !selectedCommercial}
                    >
                      <UserPlus className="w-4 h-4" />
                    </Button>
                  )}
                  {status === PROSPECT_STATUS.EN_RELANCE && (
                    <Button 
                      variant="ghost" 
                      size="sm" 
                      title="Programmer relance"
                    >
                      <Calendar className="w-4 h-4" />
                    </Button>
                  )}
                </div>
              </div>
            </div>
            
            <div className="mt-3 flex items-center justify-between">
              <div className="flex items-center space-x-4">
                <p className="text-xs text-gray-500">
                  Créé {formatDateTime(prospect.createdAt)}
                </p>
                {prospect.assignedToId && (
                  <p className="text-xs text-gray-500">• Assigné</p>
                )}
                {prospect.lastContactDate && (
                  <p className="text-xs text-success">
                    • Dernier contact: {formatDateTime(prospect.lastContactDate)}
                  </p>
                )}
                {prospect.nextFollowUpDate && (
                  <p className="text-xs text-warning">
                    • Relance prévue: {formatDateTime(prospect.nextFollowUpDate)}
                  </p>
                )}
              </div>
              
              <div className="flex items-center space-x-2">
                {status !== PROSPECT_STATUS.CONVERTI && (
                  <Select
                    value={prospect.status}
                    onValueChange={(newStatus) => handleStatusChange(prospect.id, newStatus)}
                  >
                    <SelectTrigger className="w-32 h-8 text-xs">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {Object.entries(PROSPECT_STATUS_LABELS).map(([value, label]) => (
                        <SelectItem key={value} value={value} className="text-xs">
                          {label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                )}
                
                {status === PROSPECT_STATUS.NOUVEAU && !prospect.assignedToId && (
                  <Button 
                    variant="outline" 
                    size="sm"
                    onClick={() => handleAssignProspect(prospect.id)}
                    disabled={assignProspectMutation.isPending || !selectedCommercial}
                  >
                    Assigner
                  </Button>
                )}
              </div>
            </div>
          </CardContent>
        </Card>
      ))}
    </div>
  );
}
