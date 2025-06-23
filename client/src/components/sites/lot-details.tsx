import { useState } from "react";
import { useMutation, useQuery } from "@tanstack/react-query";
import { useToast } from "@/hooks/use-toast";
import { isUnauthorizedError } from "@/lib/authUtils";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { 
  formatCurrency, 
  formatDateTime, 
  LOT_STATUS, 
  LOT_STATUS_LABELS,
  LOT_POSITION_LABELS
} from "@/lib/constants";
import { 
  MapPin, 
  DollarSign, 
  User, 
  Calendar, 
  Clock,
  AlertTriangle
} from "lucide-react";

interface LotDetailsProps {
  lot: any;
  site: any;
  onReserve?: (lotId: number, clientId: number, isTemporary?: boolean) => void;
  onClose?: () => void;
}

export default function LotDetails({ lot, site, onReserve, onClose }: LotDetailsProps) {
  const { toast } = useToast();
  const [selectedProspect, setSelectedProspect] = useState<string>("");
  const [isTemporary, setIsTemporary] = useState(false);

  const { data: prospects } = useQuery({
    queryKey: ["/api/prospects", { status: "interesse", limit: 50 }],
    retry: false,
  });

  const releaseLotMutation = useMutation({
    mutationFn: async () => {
      await apiRequest("POST", `/api/lots/${lot.id}/release`);
    },
    onSuccess: () => {
      toast({
        title: "Succès",
        description: "Lot libéré avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/sites"] });
      onClose?.();
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
        description: "Impossible de libérer le lot",
        variant: "destructive",
      });
    },
  });

  const getStatusColor = (status: string) => {
    switch (status) {
      case LOT_STATUS.DISPONIBLE:
        return "bg-success/10 text-success";
      case LOT_STATUS.RESERVE_TEMPORAIRE:
        return "bg-orange-100 text-orange-800";
      case LOT_STATUS.RESERVE:
        return "bg-warning/10 text-warning";
      case LOT_STATUS.VENDU:
        return "bg-gray-100 text-gray-600";
      default:
        return "bg-gray-100 text-gray-600";
    }
  };

  const handleReserve = () => {
    if (!selectedProspect) {
      toast({
        title: "Erreur",
        description: "Veuillez sélectionner un prospect",
        variant: "destructive",
      });
      return;
    }
    onReserve?.(lot.id, parseInt(selectedProspect), isTemporary);
  };

  const isReservationExpired = lot.reservedUntil && new Date(lot.reservedUntil) < new Date();

  return (
    <div className="space-y-6">
      {/* Lot Header */}
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-2xl font-bold text-gray-900">Lot {lot.lotNumber}</h3>
          <p className="text-gray-600">{site.name} - {site.location}</p>
        </div>
        <Badge className={getStatusColor(lot.status)}>
          {LOT_STATUS_LABELS[lot.status as keyof typeof LOT_STATUS_LABELS]}
        </Badge>
      </div>

      {/* Lot Information */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center">
            <MapPin className="w-5 h-5 mr-2" />
            Informations du lot
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="text-sm font-medium text-gray-500">Position</label>
              <p className="text-lg font-semibold">
                {LOT_POSITION_LABELS[lot.position as keyof typeof LOT_POSITION_LABELS]}
              </p>
            </div>
            <div>
              <label className="text-sm font-medium text-gray-500">Surface</label>
              <p className="text-lg font-semibold">
                {lot.surface ? `${lot.surface} m²` : "Non spécifiée"}
              </p>
            </div>
            <div>
              <label className="text-sm font-medium text-gray-500">Prix de base</label>
              <p className="text-lg font-semibold">{formatCurrency(lot.basePrice)}</p>
            </div>
            <div>
              <label className="text-sm font-medium text-gray-500">Prix final</label>
              <p className="text-lg font-semibold text-primary">{formatCurrency(lot.finalPrice)}</p>
            </div>
          </div>

          {lot.finalPrice !== lot.basePrice && (
            <div className="p-3 bg-blue-50 rounded-lg">
              <p className="text-sm text-blue-800">
                <span className="font-medium">Ajustement de prix :</span> {" "}
                {lot.finalPrice > lot.basePrice ? "+" : ""}
                {formatCurrency(lot.finalPrice - lot.basePrice)} selon la position
              </p>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Client Information (if reserved/sold) */}
      {lot.clientId && (
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center">
              <User className="w-5 h-5 mr-2" />
              Client associé
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center justify-between">
              <div>
                <p className="font-medium">Client #{lot.clientId}</p>
                <p className="text-sm text-gray-500">
                  Réservé le {formatDateTime(lot.updatedAt)}
                </p>
              </div>
              <Button variant="outline" size="sm">
                Voir le dossier client
              </Button>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Reservation Details (if temporarily reserved) */}
      {lot.status === LOT_STATUS.RESERVE_TEMPORAIRE && lot.reservedUntil && (
        <Card className={isReservationExpired ? "border-destructive bg-destructive/5" : "border-warning bg-warning/5"}>
          <CardHeader>
            <CardTitle className="flex items-center">
              <Clock className="w-5 h-5 mr-2" />
              Réservation temporaire
              {isReservationExpired && <AlertTriangle className="w-4 h-4 ml-2 text-destructive" />}
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-2">
              <p className="text-sm">
                <span className="font-medium">Expire le :</span> {formatDateTime(lot.reservedUntil)}
              </p>
              {isReservationExpired ? (
                <p className="text-sm text-destructive font-medium">
                  ⚠️ Réservation expirée - Le lot peut être libéré
                </p>
              ) : (
                <p className="text-sm text-warning">
                  Temps restant : {Math.ceil((new Date(lot.reservedUntil).getTime() - new Date().getTime()) / (1000 * 60 * 60))}h
                </p>
              )}
            </div>
          </CardContent>
        </Card>
      )}

      {/* Actions */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center">
            <DollarSign className="w-5 h-5 mr-2" />
            Actions disponibles
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          {lot.status === LOT_STATUS.DISPONIBLE && (
            <div className="space-y-4">
              <div>
                <label className="text-sm font-medium text-gray-700 mb-2 block">
                  Sélectionner un prospect
                </label>
                <Select value={selectedProspect} onValueChange={setSelectedProspect}>
                  <SelectTrigger>
                    <SelectValue placeholder="Choisir un prospect" />
                  </SelectTrigger>
                  <SelectContent>
                    {prospects?.map((prospect: any) => (
                      <SelectItem key={prospect.id} value={prospect.id.toString()}>
                        {prospect.firstName} {prospect.lastName} - {prospect.phone}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="flex items-center space-x-2">
                <input
                  type="checkbox"
                  id="temporary"
                  checked={isTemporary}
                  onChange={(e) => setIsTemporary(e.target.checked)}
                  className="rounded border-gray-300"
                />
                <label htmlFor="temporary" className="text-sm text-gray-700">
                  Réservation temporaire (48h)
                </label>
              </div>

              <div className="flex space-x-2">
                <Button
                  onClick={handleReserve}
                  disabled={!selectedProspect}
                  className="flex-1"
                >
                  {isTemporary ? "Réserver temporairement" : "Réserver définitivement"}
                </Button>
              </div>
            </div>
          )}

          {(lot.status === LOT_STATUS.RESERVE_TEMPORAIRE || 
            (lot.status === LOT_STATUS.RESERVE && isReservationExpired)) && (
            <div className="space-y-2">
              <Button
                variant="destructive"
                onClick={() => releaseLotMutation.mutate()}
                disabled={releaseLotMutation.isPending}
                className="w-full"
              >
                {releaseLotMutation.isPending ? "Libération..." : "Libérer le lot"}
              </Button>
              <p className="text-xs text-gray-500 text-center">
                Cette action rendra le lot disponible à nouveau
              </p>
            </div>
          )}

          {lot.status === LOT_STATUS.RESERVE && !isReservationExpired && (
            <div className="space-y-2">
              <Button variant="outline" className="w-full">
                Convertir en vente
              </Button>
              <Button variant="outline" className="w-full">
                Générer le contrat
              </Button>
            </div>
          )}

          {lot.status === LOT_STATUS.VENDU && (
            <div className="text-center p-4 bg-gray-50 rounded-lg">
              <p className="text-sm text-gray-600 mb-2">Ce lot a été vendu</p>
              <Button variant="outline" size="sm">
                Voir le contrat
              </Button>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Site Information */}
      <Card>
        <CardHeader>
          <CardTitle>Informations du site</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span className="text-gray-500">Frais d'adhésion :</span>
              <span className="font-medium ml-2">{formatCurrency(site.adhesionFee)}</span>
            </div>
            <div>
              <span className="text-gray-500">Frais de réservation :</span>
              <span className="font-medium ml-2">{formatCurrency(site.reservationFee)}</span>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Footer Actions */}
      <div className="flex justify-end space-x-2 pt-4 border-t">
        <Button variant="outline" onClick={onClose}>
          Fermer
        </Button>
        <Button>
          Historique des actions
        </Button>
      </div>
    </div>
  );
}
