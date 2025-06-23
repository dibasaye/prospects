import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import { setupAuth, simpleAuth } from "./replitAuth";

import { 
  insertProspectSchema, 
  insertSiteSchema, 
  insertLotSchema, 
  insertPaymentSchema, 
  insertContractSchema 
} from "@shared/schema";
import { z } from "zod";
import { fromZodError } from "zod-validation-error";

export async function registerRoutes(app: Express): Promise<Server> {
  // Auth middleware
  await setupAuth(app);

  // Simple auth system
  app.post('/api/auth/login', async (req, res) => {
    try {
      const { email, password } = req.body;
      
      // Demo accounts - in production, these would be hashed passwords in database
      const demoAccounts = {
        'admin@yayedia.com': { password: 'admin123', role: 'administrateur', id: 'admin-1', firstName: 'Admin', lastName: 'YAYE DIA' },
        'manager@yayedia.com': { password: 'manager123', role: 'responsable_commercial', id: 'manager-1', firstName: 'Manager', lastName: 'Commercial' },
        'commercial@yayedia.com': { password: 'commercial123', role: 'commercial', id: 'commercial-1', firstName: 'Agent', lastName: 'Commercial' }
      };

      const account = demoAccounts[email as keyof typeof demoAccounts];
      if (!account || account.password !== password) {
        return res.status(401).json({ message: "Email ou mot de passe incorrect" });
      }

      // Create or update user in database
      await storage.upsertUser({
        id: account.id,
        email: email,
        firstName: account.firstName,
        lastName: account.lastName,
        role: account.role as any,
        profileImageUrl: null,
      });

      // Set session
      (req as any).session.userId = account.id;
      (req as any).session.userEmail = email;
      (req as any).session.userRole = account.role;

      res.json({ 
        success: true, 
        user: { 
          id: account.id, 
          email, 
          firstName: account.firstName, 
          lastName: account.lastName, 
          role: account.role 
        } 
      });
    } catch (error) {
      console.error("Login error:", error);
      res.status(500).json({ message: "Erreur de connexion" });
    }
  });

  app.post('/api/auth/logout', (req, res) => {
    (req as any).session.destroy((err: any) => {
      if (err) {
        return res.status(500).json({ message: "Erreur de déconnexion" });
      }
      res.json({ success: true });
    });
  });

  // Simple auth middleware
  const simpleAuth = (req: any, res: any, next: any) => {
    if (!req.session?.userId) {
      return res.status(401).json({ message: "Unauthorized" });
    }
    req.user = {
      id: req.session.userId,
      email: req.session.userEmail,
      role: req.session.userRole
    };
    next();
  };

  // Auth routes
  app.get('/api/auth/user', simpleAuth, async (req: any, res) => {
    try {
      const userId = req.user.id;
      const user = await storage.getUser(userId);
      res.json(user);
    } catch (error) {
      console.error("Error fetching user:", error);
      res.status(500).json({ message: "Failed to fetch user" });
    }
  });

  // Dashboard routes
  app.get('/api/dashboard/stats', simpleAuth, async (req, res) => {
    try {
      const stats = await storage.getDashboardStats();
      res.json(stats);
    } catch (error) {
      console.error("Error fetching dashboard stats:", error);
      res.status(500).json({ message: "Failed to fetch dashboard stats" });
    }
  });

  app.get('/api/dashboard/activities', simpleAuth, async (req, res) => {
    try {
      const limit = req.query.limit ? parseInt(req.query.limit as string) : 10;
      const activities = await storage.getRecentActivities(limit);
      res.json(activities);
    } catch (error) {
      console.error("Error fetching activities:", error);
      res.status(500).json({ message: "Failed to fetch activities" });
    }
  });

  // Prospect routes
  app.get('/api/prospects', simpleAuth, async (req, res) => {
    try {
      const filters = {
        status: req.query.status as string,
        assignedToId: req.query.assignedToId as string,
        siteId: req.query.siteId ? parseInt(req.query.siteId as string) : undefined,
        limit: req.query.limit ? parseInt(req.query.limit as string) : undefined,
        offset: req.query.offset ? parseInt(req.query.offset as string) : undefined,
      };
      
      const prospects = await storage.getProspects(filters);
      res.json(prospects);
    } catch (error) {
      console.error("Error fetching prospects:", error);
      res.status(500).json({ message: "Failed to fetch prospects" });
    }
  });

  app.get('/api/prospects/:id', simpleAuth, async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const prospect = await storage.getProspect(id);
      if (!prospect) {
        return res.status(404).json({ message: "Prospect not found" });
      }
      res.json(prospect);
    } catch (error) {
      console.error("Error fetching prospect:", error);
      res.status(500).json({ message: "Failed to fetch prospect" });
    }
  });

  app.post('/api/prospects', simpleAuth, async (req: any, res) => {
    try {
      const prospectData = insertProspectSchema.parse({
        ...req.body,
        createdById: req.user.claims.sub,
      });
      
      const prospect = await storage.createProspect(prospectData);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'create',
        'prospect',
        prospect.id,
        `Nouveau prospect créé: ${prospect.firstName} ${prospect.lastName}`
      );
      
      res.status(201).json(prospect);
    } catch (error) {
      if (error instanceof z.ZodError) {
        const validationError = fromZodError(error);
        return res.status(400).json({ message: validationError.message });
      }
      console.error("Error creating prospect:", error);
      res.status(500).json({ message: "Failed to create prospect" });
    }
  });

  app.put('/api/prospects/:id', simpleAuth, async (req: any, res) => {
    try {
      const id = parseInt(req.params.id);
      const prospectData = insertProspectSchema.partial().parse(req.body);
      
      const prospect = await storage.updateProspect(id, prospectData);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'update',
        'prospect',
        id,
        `Prospect modifié: ${prospect.firstName} ${prospect.lastName}`
      );
      
      res.json(prospect);
    } catch (error) {
      if (error instanceof z.ZodError) {
        const validationError = fromZodError(error);
        return res.status(400).json({ message: validationError.message });
      }
      console.error("Error updating prospect:", error);
      res.status(500).json({ message: "Failed to update prospect" });
    }
  });

  app.post('/api/prospects/:id/assign', simpleAuth, async (req: any, res) => {
    try {
      const prospectId = parseInt(req.params.id);
      const { commercialId } = req.body;
      
      await storage.assignProspectToCommercial(prospectId, commercialId);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'assign',
        'prospect',
        prospectId,
        `Prospect assigné au commercial ${commercialId}`
      );
      
      res.json({ success: true });
    } catch (error) {
      console.error("Error assigning prospect:", error);
      res.status(500).json({ message: "Failed to assign prospect" });
    }
  });

  // Site routes
  app.get('/api/sites', simpleAuth, async (req, res) => {
    try {
      const filters = {
        isActive: req.query.isActive === 'true' ? true : req.query.isActive === 'false' ? false : undefined,
      };
      
      const sites = await storage.getSites(filters);
      res.json(sites);
    } catch (error) {
      console.error("Error fetching sites:", error);
      res.status(500).json({ message: "Failed to fetch sites" });
    }
  });

  app.get('/api/sites/:id', simpleAuth, async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const site = await storage.getSite(id);
      if (!site) {
        return res.status(404).json({ message: "Site not found" });
      }
      res.json(site);
    } catch (error) {
      console.error("Error fetching site:", error);
      res.status(500).json({ message: "Failed to fetch site" });
    }
  });

  app.post('/api/sites', simpleAuth, async (req: any, res) => {
    try {
      const siteData = insertSiteSchema.parse(req.body);
      const site = await storage.createSite(siteData);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'create',
        'site',
        site.id,
        `Nouveau site créé: ${site.name}`
      );
      
      res.status(201).json(site);
    } catch (error) {
      if (error instanceof z.ZodError) {
        const validationError = fromZodError(error);
        return res.status(400).json({ message: validationError.message });
      }
      console.error("Error creating site:", error);
      res.status(500).json({ message: "Failed to create site" });
    }
  });

  app.get('/api/sites/:id/stats', simpleAuth, async (req, res) => {
    try {
      const siteId = parseInt(req.params.id);
      const stats = await storage.getSiteStats(siteId);
      res.json(stats);
    } catch (error) {
      console.error("Error fetching site stats:", error);
      res.status(500).json({ message: "Failed to fetch site stats" });
    }
  });

  // Lot routes
  app.get('/api/sites/:siteId/lots', simpleAuth, async (req, res) => {
    try {
      const siteId = parseInt(req.params.siteId);
      const lots = await storage.getLotsBySite(siteId);
      res.json(lots);
    } catch (error) {
      console.error("Error fetching lots:", error);
      res.status(500).json({ message: "Failed to fetch lots" });
    }
  });

  app.get('/api/lots/:id', simpleAuth, async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const lot = await storage.getLot(id);
      if (!lot) {
        return res.status(404).json({ message: "Lot not found" });
      }
      res.json(lot);
    } catch (error) {
      console.error("Error fetching lot:", error);
      res.status(500).json({ message: "Failed to fetch lot" });
    }
  });

  app.post('/api/lots', simpleAuth, async (req: any, res) => {
    try {
      const lotData = insertLotSchema.parse(req.body);
      const lot = await storage.createLot(lotData);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'create',
        'lot',
        lot.id,
        `Nouveau lot créé: ${lot.lotNumber}`
      );
      
      res.status(201).json(lot);
    } catch (error) {
      if (error instanceof z.ZodError) {
        const validationError = fromZodError(error);
        return res.status(400).json({ message: validationError.message });
      }
      console.error("Error creating lot:", error);
      res.status(500).json({ message: "Failed to create lot" });
    }
  });

  app.post('/api/lots/:id/reserve', simpleAuth, async (req: any, res) => {
    try {
      const lotId = parseInt(req.params.id);
      const { clientId, isTemporary } = req.body;
      
      const lot = await storage.reserveLot(lotId, clientId, isTemporary);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'reserve',
        'lot',
        lotId,
        `Lot ${lot.lotNumber} ${isTemporary ? 'temporairement ' : ''}réservé`
      );
      
      res.json(lot);
    } catch (error) {
      console.error("Error reserving lot:", error);
      res.status(500).json({ message: "Failed to reserve lot" });
    }
  });

  app.post('/api/lots/:id/release', simpleAuth, async (req: any, res) => {
    try {
      const lotId = parseInt(req.params.id);
      const lot = await storage.releaseLot(lotId);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'release',
        'lot',
        lotId,
        `Lot ${lot.lotNumber} libéré`
      );
      
      res.json(lot);
    } catch (error) {
      console.error("Error releasing lot:", error);
      res.status(500).json({ message: "Failed to release lot" });
    }
  });

  // Payment routes
  app.get('/api/payments', simpleAuth, async (req, res) => {
    try {
      const filters = {
        clientId: req.query.clientId ? parseInt(req.query.clientId as string) : undefined,
        siteId: req.query.siteId ? parseInt(req.query.siteId as string) : undefined,
        type: req.query.type as string,
        limit: req.query.limit ? parseInt(req.query.limit as string) : undefined,
        offset: req.query.offset ? parseInt(req.query.offset as string) : undefined,
      };
      
      const payments = await storage.getPayments(filters);
      res.json(payments);
    } catch (error) {
      console.error("Error fetching payments:", error);
      res.status(500).json({ message: "Failed to fetch payments" });
    }
  });

  app.post('/api/payments', simpleAuth, async (req: any, res) => {
    try {
      const paymentData = insertPaymentSchema.parse({
        ...req.body,
        createdById: req.user.claims.sub,
      });
      
      const payment = await storage.createPayment(paymentData);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'create',
        'payment',
        payment.id,
        `Paiement enregistré: ${payment.amount} FCFA (${payment.type})`
      );
      
      res.status(201).json(payment);
    } catch (error) {
      if (error instanceof z.ZodError) {
        const validationError = fromZodError(error);
        return res.status(400).json({ message: validationError.message });
      }
      console.error("Error creating payment:", error);
      res.status(500).json({ message: "Failed to create payment" });
    }
  });

  // Contract routes
  app.get('/api/contracts', simpleAuth, async (req, res) => {
    try {
      const filters = {
        clientId: req.query.clientId ? parseInt(req.query.clientId as string) : undefined,
        status: req.query.status as string,
        limit: req.query.limit ? parseInt(req.query.limit as string) : undefined,
        offset: req.query.offset ? parseInt(req.query.offset as string) : undefined,
      };
      
      const contracts = await storage.getContracts(filters);
      res.json(contracts);
    } catch (error) {
      console.error("Error fetching contracts:", error);
      res.status(500).json({ message: "Failed to fetch contracts" });
    }
  });

  app.post('/api/contracts', simpleAuth, async (req: any, res) => {
    try {
      const contractNumber = `CTR-${Date.now()}`;
      const contractData = insertContractSchema.parse({
        ...req.body,
        contractNumber,
        createdById: req.user.claims.sub,
      });
      
      const contract = await storage.createContract(contractData);
      
      // Generate payment schedule
      await storage.generatePaymentSchedule(contract.id);
      
      // Log activity
      await storage.logActivity(
        req.user.claims.sub,
        'create',
        'contract',
        contract.id,
        `Contrat créé: ${contract.contractNumber}`
      );
      
      res.status(201).json(contract);
    } catch (error) {
      if (error instanceof z.ZodError) {
        const validationError = fromZodError(error);
        return res.status(400).json({ message: validationError.message });
      }
      console.error("Error creating contract:", error);
      res.status(500).json({ message: "Failed to create contract" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}
