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
import { Separator } from "@/components/ui/separator";
import { Badge } from "@/components/ui/badge";
import { formatCurrency, formatDate } from "@/lib/constants";
import { 
  FileText, 
  Download, 
  Calculator,
  User,
  MapPin,
  Calendar
} from "lucide-react";

const contractFormSchema = z.object({
  clientId: z.string().min(1, "Le client est requis"),
  siteId: z.string().min(1, "Le site est requis"),
  lotId: z.string().min(1, "Le lot est requis"),
  totalAmount: z.string().min(1, "Le montant total est requis"),
  paymentDuration: z.string().min(1, "La durée de paiement est requise"),
});

type ContractFormData = z.infer<typeof contractFormSchema>;

interface ContractGeneratorProps {
  onSuccess?: () => void;
  preselectedClient?: number;
  preselectedSite?: number;
  preselectedLot?: number;
}

export default function ContractGenerator({ 
  onSuccess, 
  preselectedClient,
  preselectedSite,
  preselectedLot 
}: ContractGeneratorProps) {
  const { toast } = useToast();
  const { user } = useAuth();
  const [selectedSite, setSelectedSite] = useState<string>(preselectedSite?.toString() || "");
  const [selectedLot, setSelectedLot] = useState<string>(preselectedLot?.toString() || "");
  const [isPreviewOpen, setIsPreviewOpen] = useState(false);

  const form = useForm<ContractFormData>({
    resolver: zodResolver(contractFormSchema),
    defaultValues: {
      clientId: preselectedClient?.toString() || "",
      siteId: preselectedSite?.toString() || "",
      lotId: preselectedLot?.toString() || "",
      totalAmount: "",
      paymentDuration: "24",
    },
  });

  const watchedAmount = form.watch("totalAmount");
  const watchedDuration = form.watch("paymentDuration");

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

  const { data: client } = useQuery({
    queryKey: ["/api/prospects", form.watch("clientId")],
    enabled: !!form.watch("clientId"),
    retry: false,
  });

  const { data: site } = useQuery({
    queryKey: ["/api/sites", selectedSite],
    enabled: !!selectedSite,
    retry: false,
  });

  const { data: lot } = useQuery({
    queryKey: ["/api/lots", selectedLot],
    enabled: !!selectedLot,
    retry: false,
  });

  const createContractMutation = useMutation({
    mutationFn: async (data: ContractFormData) => {
      const payload = {
        clientId: parseInt(data.clientId),
        siteId: parseInt(data.siteId),
        lotId: parseInt(data.lotId),
        totalAmount: parseInt(data.totalAmount),
        paymentDuration: parseInt(data.paymentDuration),
        monthlyAmount: Math.ceil(parseInt(data.totalAmount) / parseInt(data.paymentDuration)),
        paidAmount: 0,
        status: "brouillon",
        createdById: user?.id,
      };
      
      const response = await apiRequest("POST", "/api/contracts", payload);
      return response.json();
    },
    onSuccess: (contract) => {
      toast({
        title: "Succès",
        description: "Contrat créé avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/contracts"] });
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
        description: "Impossible de créer le contrat",
        variant: "destructive",
      });
    },
  });

  const calculateMonthlyAmount = () => {
    if (!watchedAmount || !watchedDuration) return 0;
    return Math.ceil(parseInt(watchedAmount) / parseInt(watchedDuration));
  };

  const handleSiteChange = (siteId: string) => {
    setSelectedSite(siteId);
    form.setValue("siteId", siteId);
    form.setValue("lotId", ""); // Reset lot selection
    setSelectedLot("");
  };

  const handleLotChange = (lotId: string) => {
    setSelectedLot(lotId);
    form.setValue("lotId", lotId);
    
    // Auto-fill total amount with lot price
    const selectedLotData = lots?.find((l: any) => l.id.toString() === lotId);
    if (selectedLotData) {
      form.setValue("totalAmount", selectedLotData.finalPrice.toString());
    }
  };

  const onSubmit = (data: ContractFormData) => {
    createContractMutation.mutate(data);
  };

  return (
    <div className="space-y-6">
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
          {/* Sélection du client et de la propriété */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <User className="w-5 h-5 mr-2" />
                Sélection du client et de la propriété
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                              {prospect.firstName} {prospect.lastName}
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
                              {site.name}
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
                      <FormLabel>Lot *</FormLabel>
                      <Select onValueChange={handleLotChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Sélectionnez un lot" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {lots?.filter((lot: any) => lot.status === "reserve").map((lot: any) => (
                            <SelectItem key={lot.id} value={lot.id.toString()}>
                              Lot {lot.lotNumber} - {formatCurrency(lot.finalPrice)}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      <FormDescription>
                        Seuls les lots réservés sont disponibles pour la création de contrat
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              {/* Client Info Display */}
              {client && (
                <div className="p-4 bg-blue-50 rounded-lg">
                  <h4 className="font-medium text-blue-900 mb-2">Informations client</h4>
                  <div className="grid grid-cols-2 gap-2 text-sm">
                    <div><span className="text-blue-700">Nom:</span> {client.firstName} {client.lastName}</div>
                    <div><span className="text-blue-700">Téléphone:</span> {client.phone}</div>
                    {client.email && <div><span className="text-blue-700">Email:</span> {client.email}</div>}
                    {client.address && <div className="col-span-2"><span className="text-blue-700">Adresse:</span> {client.address}</div>}
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Détails financiers */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <Calculator className="w-5 h-5 mr-2" />
                Configuration financière
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="totalAmount"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Montant total (FCFA) *</FormLabel>
                      <FormControl>
                        <Input 
                          type="number" 
                          placeholder="5000000" 
                          {...field}
                        />
                      </FormControl>
                      {lot && (
                        <FormDescription>
                          Prix du lot: {formatCurrency(lot.finalPrice)}
                        </FormDescription>
                      )}
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="paymentDuration"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Durée de paiement *</FormLabel>
                      <Select onValueChange={field.onChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Durée" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="12">12 mois (1 an)</SelectItem>
                          <SelectItem value="24">24 mois (2 ans)</SelectItem>
                          <SelectItem value="36">36 mois (3 ans)</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              {/* Calcul automatique */}
              {watchedAmount && watchedDuration && (
                <div className="p-4 bg-green-50 rounded-lg">
                  <h4 className="font-medium text-green-900 mb-2">Calcul des mensualités</h4>
                  <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span className="text-green-700">Montant total:</span>
                      <div className="text-lg font-bold text-green-900">
                        {formatCurrency(parseInt(watchedAmount))}
                      </div>
                    </div>
                    <div>
                      <span className="text-green-700">Mensualité:</span>
                      <div className="text-lg font-bold text-green-900">
                        {formatCurrency(calculateMonthlyAmount())}
                      </div>
                    </div>
                    <div>
                      <span className="text-green-700">Durée:</span>
                      <div className="font-medium">{watchedDuration} mois</div>
                    </div>
                    <div>
                      <span className="text-green-700">Date de fin:</span>
                      <div className="font-medium">
                        {formatDate(new Date(Date.now() + parseInt(watchedDuration) * 30 * 24 * 60 * 60 * 1000))}
                      </div>
                    </div>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Résumé du contrat */}
          {client && site && lot && watchedAmount && watchedDuration && (
            <Card className="bg-primary/5 border-primary/20">
              <CardHeader>
                <CardTitle className="flex items-center text-primary">
                  <FileText className="w-5 h-5 mr-2" />
                  Aperçu du contrat
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h4 className="font-semibold mb-2">Informations client</h4>
                    <div className="space-y-1 text-sm">
                      <div>{client.firstName} {client.lastName}</div>
                      <div>{client.phone}</div>
                      {client.email && <div>{client.email}</div>}
                    </div>
                  </div>
                  <div>
                    <h4 className="font-semibold mb-2">Propriété</h4>
                    <div className="space-y-1 text-sm">
                      <div><span className="font-medium">Site:</span> {site.name}</div>
                      <div><span className="font-medium">Lot:</span> {lot.lotNumber}</div>
                      <div><span className="font-medium">Position:</span> {lot.position}</div>
                      <div><span className="font-medium">Surface:</span> {lot.surface} m²</div>
                    </div>
                  </div>
                </div>

                <Separator />

                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                  <div>
                    <div className="text-2xl font-bold text-primary">
                      {formatCurrency(parseInt(watchedAmount))}
                    </div>
                    <div className="text-xs text-gray-600">Montant total</div>
                  </div>
                  <div>
                    <div className="text-2xl font-bold text-green-600">
                      {formatCurrency(calculateMonthlyAmount())}
                    </div>
                    <div className="text-xs text-gray-600">Par mois</div>
                  </div>
                  <div>
                    <div className="text-2xl font-bold text-blue-600">
                      {watchedDuration}
                    </div>
                    <div className="text-xs text-gray-600">Mois</div>
                  </div>
                  <div>
                    <div className="text-2xl font-bold text-orange-600">
                      0
                    </div>
                    <div className="text-xs text-gray-600">Payé</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          )}

          {/* Actions */}
          <div className="flex justify-between">
            <div className="flex space-x-2">
              <Button 
                type="button" 
                variant="outline"
                onClick={() => setIsPreviewOpen(true)}
                disabled={!client || !site || !lot || !watchedAmount}
              >
                <FileText className="w-4 h-4 mr-2" />
                Aperçu
              </Button>
            </div>
            <div className="flex space-x-2">
              <Button 
                type="button" 
                variant="outline" 
                onClick={() => form.reset()}
                disabled={createContractMutation.isPending}
              >
                Annuler
              </Button>
              <Button 
                type="submit" 
                disabled={createContractMutation.isPending || !client || !site || !lot}
              >
                {createContractMutation.isPending ? "Génération..." : "Générer le contrat"}
              </Button>
            </div>
          </div>
        </form>
      </Form>

      {/* Contract Preview Modal would go here */}
      {/* This would be implemented as a separate component with PDF preview */}
    </div>
  );
}
