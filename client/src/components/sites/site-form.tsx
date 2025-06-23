import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { z } from "zod";
import { useToast } from "@/hooks/use-toast";
import { isUnauthorizedError } from "@/lib/authUtils";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Switch } from "@/components/ui/switch";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Card, CardContent } from "@/components/ui/card";

const siteFormSchema = z.object({
  name: z.string().min(1, "Le nom du site est requis"),
  location: z.string().min(1, "La localisation est requise"),
  department: z.string().optional(),
  commune: z.string().optional(),
  gpsCoordinates: z.string().optional(),
  adhesionFee: z.string().min(1, "Les frais d'adhésion sont requis"),
  reservationFee: z.string().min(1, "Les frais de réservation sont requis"),
  totalLots: z.string().min(1, "Le nombre total de lots est requis"),
  launchDate: z.string().optional(),
  isActive: z.boolean().default(true),
});

type SiteFormData = z.infer<typeof siteFormSchema>;

interface SiteFormProps {
  onSuccess?: () => void;
  initialData?: Partial<SiteFormData>;
}

export default function SiteForm({ onSuccess, initialData }: SiteFormProps) {
  const { toast } = useToast();

  const form = useForm<SiteFormData>({
    resolver: zodResolver(siteFormSchema),
    defaultValues: {
      name: initialData?.name || "",
      location: initialData?.location || "",
      department: initialData?.department || "",
      commune: initialData?.commune || "",
      gpsCoordinates: initialData?.gpsCoordinates || "",
      adhesionFee: initialData?.adhesionFee || "",
      reservationFee: initialData?.reservationFee || "",
      totalLots: initialData?.totalLots || "",
      launchDate: initialData?.launchDate || "",
      isActive: initialData?.isActive ?? true,
    },
  });

  const createSiteMutation = useMutation({
    mutationFn: async (data: SiteFormData) => {
      const payload = {
        ...data,
        adhesionFee: parseInt(data.adhesionFee),
        reservationFee: parseInt(data.reservationFee),
        totalLots: parseInt(data.totalLots),
        launchDate: data.launchDate ? new Date(data.launchDate).toISOString() : null,
      };
      
      await apiRequest("POST", "/api/sites", payload);
    },
    onSuccess: () => {
      toast({
        title: "Succès",
        description: "Site créé avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/sites"] });
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
        description: "Impossible de créer le site",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: SiteFormData) => {
    createSiteMutation.mutate(data);
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
        {/* Informations générales */}
        <Card>
          <CardContent className="p-4">
            <h3 className="text-lg font-semibold mb-4">Informations générales</h3>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Nom du site *</FormLabel>
                    <FormControl>
                      <Input placeholder="Ex: Keur Ndiaye Lo" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="location"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Localisation *</FormLabel>
                    <FormControl>
                      <Input placeholder="Ex: Thiès, Sénégal" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="department"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Département</FormLabel>
                    <FormControl>
                      <Input placeholder="Ex: Thiès" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="commune"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Commune</FormLabel>
                    <FormControl>
                      <Input placeholder="Ex: Thiès Nord" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="gpsCoordinates"
                render={({ field }) => (
                  <FormItem className="md:col-span-2">
                    <FormLabel>Coordonnées GPS</FormLabel>
                    <FormControl>
                      <Input placeholder="Ex: 14.7936, -16.9267" {...field} />
                    </FormControl>
                    <FormDescription>
                      Format: latitude, longitude
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
          </CardContent>
        </Card>

        {/* Configuration financière */}
        <Card>
          <CardContent className="p-4">
            <h3 className="text-lg font-semibold mb-4">Configuration financière</h3>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <FormField
                control={form.control}
                name="adhesionFee"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Frais d'adhésion (FCFA) *</FormLabel>
                    <FormControl>
                      <Input type="number" placeholder="50000" {...field} />
                    </FormControl>
                    <FormDescription>
                      Montant non remboursable
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="reservationFee"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Frais de réservation (FCFA) *</FormLabel>
                    <FormControl>
                      <Input type="number" placeholder="500000" {...field} />
                    </FormControl>
                    <FormDescription>
                      Acompte de réservation
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="totalLots"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Nombre total de lots *</FormLabel>
                    <FormControl>
                      <Input type="number" placeholder="100" {...field} />
                    </FormControl>
                    <FormDescription>
                      Lots à créer pour ce site
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
          </CardContent>
        </Card>

        {/* Configuration avancée */}
        <Card>
          <CardContent className="p-4">
            <h3 className="text-lg font-semibold mb-4">Configuration avancée</h3>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="launchDate"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Date de lancement</FormLabel>
                    <FormControl>
                      <Input type="date" {...field} />
                    </FormControl>
                    <FormDescription>
                      Date officielle de lancement du site
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="isActive"
                render={({ field }) => (
                  <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                    <div className="space-y-0.5">
                      <FormLabel className="text-base">Site actif</FormLabel>
                      <FormDescription>
                        Le site est disponible pour les commerciaux
                      </FormDescription>
                    </div>
                    <FormControl>
                      <Switch
                        checked={field.value}
                        onCheckedChange={field.onChange}
                      />
                    </FormControl>
                  </FormItem>
                )}
              />
            </div>
          </CardContent>
        </Card>

        {/* Actions */}
        <div className="flex justify-end space-x-4">
          <Button 
            type="button" 
            variant="outline" 
            onClick={() => form.reset()}
            disabled={createSiteMutation.isPending}
          >
            Annuler
          </Button>
          <Button 
            type="submit" 
            disabled={createSiteMutation.isPending}
            className="bg-success hover:bg-success/90"
          >
            {createSiteMutation.isPending ? "Création..." : "Créer le site"}
          </Button>
        </div>
      </form>
    </Form>
  );
}
