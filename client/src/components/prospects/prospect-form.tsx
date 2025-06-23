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
import { Card, CardContent } from "@/components/ui/card";
import { PROSPECT_STATUS } from "@/lib/constants";

const prospectFormSchema = z.object({
  firstName: z.string().min(1, "Le prénom est requis"),
  lastName: z.string().min(1, "Le nom est requis"),
  phone: z.string().min(8, "Le numéro de téléphone doit contenir au moins 8 chiffres"),
  phoneSecondary: z.string().optional(),
  email: z.string().email("Email invalide").optional().or(z.literal("")),
  address: z.string().optional(),
  interestedSiteId: z.string().optional(),
  notes: z.string().optional(),
  representativeName: z.string().optional(),
  representativePhone: z.string().optional(),
  representativeAddress: z.string().optional(),
});

type ProspectFormData = z.infer<typeof prospectFormSchema>;

interface ProspectFormProps {
  onSuccess?: () => void;
  initialData?: Partial<ProspectFormData>;
}

export default function ProspectForm({ onSuccess, initialData }: ProspectFormProps) {
  const { toast } = useToast();
  const { user } = useAuth();
  const [isAdvanced, setIsAdvanced] = useState(false);

  const form = useForm<ProspectFormData>({
    resolver: zodResolver(prospectFormSchema),
    defaultValues: {
      firstName: initialData?.firstName || "",
      lastName: initialData?.lastName || "",
      phone: initialData?.phone || "",
      phoneSecondary: initialData?.phoneSecondary || "",
      email: initialData?.email || "",
      address: initialData?.address || "",
      interestedSiteId: initialData?.interestedSiteId || "",
      notes: initialData?.notes || "",
      representativeName: initialData?.representativeName || "",
      representativePhone: initialData?.representativePhone || "",
      representativeAddress: initialData?.representativeAddress || "",
    },
  });

  const { data: sites } = useQuery({
    queryKey: ["/api/sites", { isActive: true }],
    retry: false,
  });

  const createProspectMutation = useMutation({
    mutationFn: async (data: ProspectFormData) => {
      const payload = {
        ...data,
        interestedSiteId: data.interestedSiteId ? parseInt(data.interestedSiteId) : null,
        status: PROSPECT_STATUS.NOUVEAU,
        createdById: user?.id,
      };
      
      await apiRequest("POST", "/api/prospects", payload);
    },
    onSuccess: () => {
      toast({
        title: "Succès",
        description: "Prospect créé avec succès",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/prospects"] });
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
        description: "Impossible de créer le prospect",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: ProspectFormData) => {
    createProspectMutation.mutate(data);
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
        {/* Informations de base */}
        <Card>
          <CardContent className="p-4">
            <h3 className="text-lg font-semibold mb-4">Informations de base</h3>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="firstName"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Prénom *</FormLabel>
                    <FormControl>
                      <Input placeholder="Entrez le prénom" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="lastName"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Nom *</FormLabel>
                    <FormControl>
                      <Input placeholder="Entrez le nom" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="phone"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Téléphone principal *</FormLabel>
                    <FormControl>
                      <Input placeholder="+221 77 123 45 67" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="phoneSecondary"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Téléphone secondaire</FormLabel>
                    <FormControl>
                      <Input placeholder="+221 70 123 45 67" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="email"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Email</FormLabel>
                    <FormControl>
                      <Input type="email" placeholder="email@exemple.com" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="interestedSiteId"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Site d'intérêt</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Sélectionnez un site" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="">Aucun site spécifique</SelectItem>
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
            </div>

            <FormField
              control={form.control}
              name="address"
              render={({ field }) => (
                <FormItem className="mt-4">
                  <FormLabel>Adresse postale</FormLabel>
                  <FormControl>
                    <Textarea 
                      placeholder="Adresse complète du prospect" 
                      className="resize-none" 
                      {...field} 
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="notes"
              render={({ field }) => (
                <FormItem className="mt-4">
                  <FormLabel>Notes</FormLabel>
                  <FormControl>
                    <Textarea 
                      placeholder="Notes sur le prospect, contexte de l'appel, etc." 
                      className="resize-none" 
                      {...field} 
                    />
                  </FormControl>
                  <FormDescription>
                    Informations supplémentaires sur le prospect
                  </FormDescription>
                  <FormMessage />
                </FormItem>
              )}
            />
          </CardContent>
        </Card>

        {/* Informations du représentant (optionnel) */}
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-semibold">Représentant (optionnel)</h3>
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={() => setIsAdvanced(!isAdvanced)}
              >
                {isAdvanced ? "Masquer" : "Afficher"}
              </Button>
            </div>

            {isAdvanced && (
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="representativeName"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Nom du représentant</FormLabel>
                      <FormControl>
                        <Input placeholder="Nom complet" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="representativePhone"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Téléphone du représentant</FormLabel>
                      <FormControl>
                        <Input placeholder="+221 77 123 45 67" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="representativeAddress"
                  render={({ field }) => (
                    <FormItem className="md:col-span-2">
                      <FormLabel>Adresse du représentant</FormLabel>
                      <FormControl>
                        <Textarea 
                          placeholder="Adresse complète du représentant" 
                          className="resize-none" 
                          {...field} 
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
            )}
          </CardContent>
        </Card>

        {/* Actions */}
        <div className="flex justify-end space-x-4">
          <Button 
            type="button" 
            variant="outline" 
            onClick={() => form.reset()}
            disabled={createProspectMutation.isPending}
          >
            Annuler
          </Button>
          <Button 
            type="submit" 
            disabled={createProspectMutation.isPending}
          >
            {createProspectMutation.isPending ? "Création..." : "Créer le prospect"}
          </Button>
        </div>
      </form>
    </Form>
  );
}
