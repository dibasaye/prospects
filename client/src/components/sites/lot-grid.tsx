import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { cn } from "@/lib/utils";
import { LOT_STATUS } from "@/lib/constants";

interface Lot {
  id: number;
  lotNumber: string;
  status: string;
  position: string;
  finalPrice: number;
  clientId?: number;
  reservedUntil?: string;
}

interface LotGridProps {
  lots: Lot[];
  onLotClick?: (lot: Lot) => void;
  onLotReserve?: (lotId: number, clientId: number, isTemporary?: boolean) => void;
  maxDisplayLots?: number;
}

export default function LotGrid({ 
  lots, 
  onLotClick, 
  onLotReserve, 
  maxDisplayLots = 40 
}: LotGridProps) {
  const [selectedLot, setSelectedLot] = useState<Lot | null>(null);

  const getLotColor = (status: string) => {
    switch (status) {
      case LOT_STATUS.DISPONIBLE:
        return "bg-success hover:bg-success/80 text-white";
      case LOT_STATUS.RESERVE_TEMPORAIRE:
        return "bg-orange-500 hover:bg-orange-600 text-white";
      case LOT_STATUS.RESERVE:
        return "bg-warning hover:bg-warning/80 text-white";
      case LOT_STATUS.VENDU:
        return "bg-gray-500 cursor-not-allowed text-white";
      default:
        return "bg-gray-300 text-gray-600";
    }
  };

  const getLotBorderColor = (status: string) => {
    switch (status) {
      case LOT_STATUS.DISPONIBLE:
        return "border-success";
      case LOT_STATUS.RESERVE_TEMPORAIRE:
        return "border-orange-500";
      case LOT_STATUS.RESERVE:
        return "border-warning";
      case LOT_STATUS.VENDU:
        return "border-gray-500";
      default:
        return "border-gray-300";
    }
  };

  const handleLotClick = (lot: Lot) => {
    if (lot.status === LOT_STATUS.VENDU) return;
    
    setSelectedLot(lot);
    onLotClick?.(lot);
  };

  const isLotClickable = (status: string) => {
    return status !== LOT_STATUS.VENDU;
  };

  const displayLots = lots.slice(0, maxDisplayLots);
  const remainingLots = lots.length - maxDisplayLots;

  // Organize lots in a grid pattern (10 columns)
  const rows: Lot[][] = [];
  const lotsPerRow = 10;
  
  for (let i = 0; i < displayLots.length; i += lotsPerRow) {
    rows.push(displayLots.slice(i, i + lotsPerRow));
  }

  return (
    <div className="space-y-4">
      {/* Grid Display */}
      <div className="space-y-2">
        {rows.map((row, rowIndex) => (
          <div key={rowIndex} className="grid grid-cols-10 gap-2">
            {row.map((lot) => (
              <Button
                key={lot.id}
                variant="outline"
                className={cn(
                  "aspect-square p-0 text-xs font-medium transition-all duration-200 border-2",
                  getLotColor(lot.status),
                  getLotBorderColor(lot.status),
                  selectedLot?.id === lot.id && "ring-2 ring-primary ring-offset-2",
                  isLotClickable(lot.status) ? "cursor-pointer transform hover:scale-105" : "cursor-not-allowed"
                )}
                onClick={() => handleLotClick(lot)}
                disabled={!isLotClickable(lot.status)}
                title={`Lot ${lot.lotNumber} - ${lot.status} - ${lot.finalPrice} FCFA`}
              >
                {lot.lotNumber}
              </Button>
            ))}
          </div>
        ))}
      </div>

      {/* Show remaining lots count */}
      {remainingLots > 0 && (
        <div className="text-center py-4">
          <p className="text-sm text-gray-500 mb-2">
            Affichage de {maxDisplayLots} lots sur {lots.length}
          </p>
          <Button variant="outline" size="sm">
            Voir tous les lots ({lots.length})
          </Button>
        </div>
      )}

      {/* Legend */}
      <div className="flex flex-wrap items-center justify-center gap-4 py-4 border-t">
        <div className="flex items-center space-x-2">
          <div className="w-4 h-4 bg-success rounded border"></div>
          <span className="text-sm text-gray-600">Disponible</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-4 h-4 bg-orange-500 rounded border"></div>
          <span className="text-sm text-gray-600">Réservé temporairement</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-4 h-4 bg-warning rounded border"></div>
          <span className="text-sm text-gray-600">Réservé</span>
        </div>
        <div className="flex items-center space-x-2">
          <div className="w-4 h-4 bg-gray-500 rounded border"></div>
          <span className="text-sm text-gray-600">Vendu</span>
        </div>
      </div>

      {/* Quick Stats */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t">
        <div className="text-center">
          <div className="text-lg font-bold text-success">
            {lots.filter(l => l.status === LOT_STATUS.DISPONIBLE).length}
          </div>
          <div className="text-sm text-gray-500">Disponibles</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-warning">
            {lots.filter(l => [LOT_STATUS.RESERVE_TEMPORAIRE, LOT_STATUS.RESERVE].includes(l.status)).length}
          </div>
          <div className="text-sm text-gray-500">Réservés</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-gray-600">
            {lots.filter(l => l.status === LOT_STATUS.VENDU).length}
          </div>
          <div className="text-sm text-gray-500">Vendus</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-gray-900">
            {((lots.filter(l => l.status !== LOT_STATUS.DISPONIBLE).length / lots.length) * 100).toFixed(0)}%
          </div>
          <div className="text-sm text-gray-500">Occupation</div>
        </div>
      </div>

      {/* Selected Lot Quick Info */}
      {selectedLot && (
        <div className="mt-4 p-4 bg-primary/5 border border-primary/20 rounded-lg">
          <div className="flex items-center justify-between">
            <div>
              <h4 className="font-medium text-gray-900">Lot {selectedLot.lotNumber}</h4>
              <p className="text-sm text-gray-600">
                Position: {selectedLot.position} • Prix: {selectedLot.finalPrice.toLocaleString()} FCFA
              </p>
              <Badge className={cn(
                "mt-1",
                selectedLot.status === LOT_STATUS.DISPONIBLE ? "bg-success/10 text-success" :
                selectedLot.status === LOT_STATUS.RESERVE_TEMPORAIRE ? "bg-orange-100 text-orange-800" :
                selectedLot.status === LOT_STATUS.RESERVE ? "bg-warning/10 text-warning" :
                "bg-gray-100 text-gray-600"
              )}>
                {selectedLot.status}
              </Badge>
            </div>
            <div className="flex space-x-2">
              {selectedLot.status === LOT_STATUS.DISPONIBLE && (
                <>
                  <Button size="sm" variant="outline">
                    Réserver temporairement
                  </Button>
                  <Button size="sm">
                    Réserver définitivement
                  </Button>
                </>
              )}
              {selectedLot.status === LOT_STATUS.RESERVE_TEMPORAIRE && (
                <Button size="sm" variant="outline">
                  Confirmer réservation
                </Button>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
