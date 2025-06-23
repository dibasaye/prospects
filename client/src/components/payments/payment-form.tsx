import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation, useQuery } from "@tanstack/react-query";
import { z } from "zod";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/hooks/useAuth";
import { isUnauthorizedError } from "@/lib/authUtils";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { PAYMENT_TYPE, PAYMENT_TYPE_LABELS, formatCurrency } from "@/lib/constants";

const paymentFormSchema = z.object({
  clientId: z.string().min(1, "Le client est requis"),
  siteId: z.string().min(1, "Le site est requis"),
  lotId: z.string().optional(),
  type: z.enum(["adhesion", "reservation", "mensualite"]),
  amount: z.string().min(1, "Le montant est requis"),
  paymentMethod: z.string().min(1, "Le mode de paiement est requis"),
  receiptNumber: z.string().optional(),
  notes: z.string().optional(),
});

type PaymentFormData = z.infer<typeof paymentFormSchema>;

interface PaymentFormProps {
  onSuccess?: () => void;
  initialData?: Partial<PaymentFormData>;
  preselectedClient?: number;
  preselectedSite?: number;
  preselectedLot?: number;
}

export default function PaymentForm({ 
  onSuccess, 
  initialData, 
  preselectedClient,
  preselectedSite,
  preselectedLot 
}: PaymentFormProps) {
  const { toast } = useToast();
  const { user } = useAuth();
  const [selectedSite, setSelectedSite] = useState<string>(preselectedSite?.toString() || "");

  const form = useForm<PaymentFormData>({
    resolver: zodResolver(paymentFormSchema),
    defaultValues: {
      clientId: preselectedClient?.toString() || initialData?.clientId || "",
      siteId: preselectedSite?.toString() || initialData?.siteId || "",
      lotId: preselectedLot?.toString() || initialData?.lotId || "",
      type: initialData?.type || "adhesion",
      amount: initialData?.amount || "",
      paymentMethod: initialData?.paymentMethod || "",
      receiptNumber: initialData?.receiptNumber || "",
      notes: initialData?.notes || "",
    },
  });

  const watchedType = form.watch("type");

  const { data: prospects } = useQuery({
    queryKey: ["/api/prospects", { status: "interesse", limit: 100 }],
    retry: false,
  });

  const { data: sites } = useQuery({
    queryKey: ["/api/sites", { isActive: true }],
    retry: false,
  });

  const { data: lots } = useQuery({
    queryKey: ["/api/sites", selectedSite, "lots"],
    enabled: !!selectedSite,
    retry: false,
  });

  const createPaymentMutation = useMutation({
    mutationFn: async (data: PaymentFormData) => {
      const payload = {
        ...data,
        clientId: parseInt(data.clientId),
        siteId: parseInt(data.siteId),
        lotId: data.lotId ? parseInt(data.lotId) : null,
        amount: parseInt(data.amount),
        paymentDate: new Date().toISOString(),
        createdById: user?.id,
      };
      
      await apiRequest("POST", "/api/payments", payload);
    },
    onSuccess: () => {
      toast({
        title: "Succès",
        description: "Paiement enregistré avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/payments"] });
      queryClient.invalidateQueries({ queryKey: ["/api/dashboard"] });
      form.reset();
      onSuccess?.();
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
        description: "Impossible d'enregistrer le paiement",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: PaymentFormData) => {
    createPaymentMutation.mutate(data);
  };

  const getSuggestedAmount = (type: string, siteId: string) => {
    if (!siteId) return "";
    const site = sites?.find((s: any) => s.id.toString() === siteId);
    if (!site) return "";

    switch (type) {
      case PAYMENT_TYPE.ADHESION:
        return site.adhesionFee.toString();
      case PAYMENT_TYPE.RESERVATION:
        return site.reservationFee.toString();
      default:
        return "";
    }
  };

  const handleSiteChange = (siteId: string) => {
    setSelectedSite(siteId);
    form.setValue("siteId", siteId);
    form.setValue("lotId", ""); // Reset lot selection
    
    // Auto-fill amount based on type and site
    const suggestedAmount = getSuggestedAmount(watchedType, siteId);
    if (suggestedAmount) {
      form.setValue("amount", suggestedAmount);
    }
  };

  const handleTypeChange = (type: string) => {
    form.setValue("type", type as any);
    
    // Auto-fill amount based on type and selected site
    const suggestedAmount = getSuggestedAmount(type, selectedSite);
    if (suggestedAmount) {
      form.setValue("amount", suggestedAmount);
    }
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
        {/* Client et Site */}
        <Card>
          <CardHeader>
            <CardTitle>Informations générales</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="clientId"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Client *</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Sélectionnez un client" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {prospects?.map((prospect: any) => (
                          <SelectItem key={prospect.id} value={prospect.id.toString()}>
                            {prospect.firstName} {prospect.lastName} - {prospect.phone}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="siteId"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Site *</FormLabel>
                    <Select onValueChange={handleSiteChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Sélectionnez un site" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {sites?.map((site: any) => (
                          <SelectItem key={site.id} value={site.id.toString()}>
                            {site.name} - {site.location}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="lotId"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Lot (optionnel)</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Sélectionnez un lot" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="">Aucun lot spécifique</SelectItem>
                        {lots?.map((lot: any) => (
                          <SelectItem key={lot.id} value={lot.id.toString()}>
                            Lot {lot.lotNumber} - {formatCurrency(lot.finalPrice)}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormDescription>
                      Requis pour les paiements de réservation et mensualités
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="type"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Type de paiement *</FormLabel>
                    <Select onValueChange={handleTypeChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Type de paiement" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        {Object.entries(PAYMENT_TYPE_LABELS).map(([value, label]) => (
                          <SelectItem key={value} value={value}>
                            {label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
          </CardContent>
        </Card>

        {/* Détails du paiement */}
        <Card>
          <CardHeader>
            <CardTitle>Détails du paiement</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="amount"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Montant (FCFA) *</FormLabel>
                    <FormControl>
                      <Input 
                        type="number" 
                        placeholder="50000" 
                        {...field}
                      />
                    </FormControl>
                    {watchedType === PAYMENT_TYPE.ADHESION && selectedSite && (
                      <FormDescription>
                        Montant suggéré: {formatCurrency(parseInt(getSuggestedAmount(watchedType, selectedSite)) || 0)}
                      </FormDescription>
                    )}
                    {watchedType === PAYMENT_TYPE.RESERVATION && selectedSite && (
                      <FormDescription>
                        Montant suggéré: {formatCurrency(parseInt(getSuggestedAmount(watchedType, selectedSite)) || 0)}
                      </FormDescription>
                    )}
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="paymentMethod"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Mode de paiement *</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Mode de paiement" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="cash">Espèces</SelectItem>
                        <SelectItem value="bank_transfer">Virement bancaire</SelectItem>
                        <SelectItem value="check">Chèque</SelectItem>
                        <SelectItem value="mobile_money">Mobile Money</SelectItem>
                        <SelectItem value="card">Carte bancaire</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="receiptNumber"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Numéro de reçu</FormLabel>
                    <FormControl>
                      <Input placeholder="REC-2024-001" {...field} />
                    </FormControl>
                    <FormDescription>
                      Numéro de référence du reçu de paiement
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <FormField
              control={form.control}
              name="notes"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Notes</FormLabel>
                  <FormControl>
                    <Textarea 
                      placeholder="Notes additionnelles sur le paiement..." 
                      className="resize-none" 
                      {...field} 
                    />
                  </FormControl>
                  <FormDescription>
                    Informations complémentaires sur le paiement
                  </FormDescription>
                  <FormMessage />
                </FormItem>
              )}
            />
          </CardContent>
        </Card>

        {/* Résumé */}
        {form.watch("amount") && form.watch("type") && (
          <Card className="bg-primary/5 border-primary/20">
            <CardHeader>
              <CardTitle className="text-primary">Résumé du paiement</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                <div className="flex justify-between">
                  <span>Type:</span>
                  <span className="font-medium">
                    {PAYMENT_TYPE_LABELS[watchedType as keyof typeof PAYMENT_TYPE_LABELS]}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span>Montant:</span>
                  <span className="font-bold text-lg">
                    {formatCurrency(parseInt(form.watch("amount")) || 0)}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span>Mode de paiement:</span>
                  <span className="font-medium">{form.watch("paymentMethod")}</span>
                </div>
              </div>
            </CardContent>
          </Card>
        )}

        {/* Actions */}
        <div className="flex justify-end space-x-4">
          <Button 
            type="button" 
            variant="outline" 
            onClick={() => form.reset()}
            disabled={createPaymentMutation.isPending}
          >
            Annuler
          </Button>
          <Button 
            type="submit" 
            disabled={createPaymentMutation.isPending}
          >
            {createPaymentMutation.isPending ? "Enregistrement..." : "Enregistrer le paiement"}
          </Button>
        </div>
      </form>
    </Form>
  );
}
