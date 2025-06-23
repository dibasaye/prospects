import {
  users,
  prospects,
  sites,
  lots,
  payments,
  contracts,
  paymentSchedule,
  activityLog,
  type User,
  type UpsertUser,
  type Prospect,
  type InsertProspect,
  type Site,
  type InsertSite,
  type Lot,
  type InsertLot,
  type Payment,
  type InsertPayment,
  type Contract,
  type InsertContract,
  type PaymentSchedule,
  type ActivityLog,
} from "@shared/schema";
import { db } from "./db";
import { eq, desc, asc, and, or, count, sql } from "drizzle-orm";

export interface IStorage {
  // User operations (mandatory for Replit Auth)
  getUser(id: string): Promise<User | undefined>;
  upsertUser(user: UpsertUser): Promise<User>;

  // Prospect operations
  getProspects(filters?: {
    status?: string;
    assignedToId?: string;
    siteId?: number;
    limit?: number;
    offset?: number;
  }): Promise<Prospect[]>;
  getProspect(id: number): Promise<Prospect | undefined>;
  createProspect(prospect: InsertProspect): Promise<Prospect>;
  updateProspect(id: number, prospect: Partial<InsertProspect>): Promise<Prospect>;
  deleteProspect(id: number): Promise<void>;
  assignProspectToCommercial(prospectId: number, commercialId: string): Promise<void>;

  // Site operations
  getSites(filters?: { isActive?: boolean }): Promise<Site[]>;
  getSite(id: number): Promise<Site | undefined>;
  createSite(site: InsertSite): Promise<Site>;
  updateSite(id: number, site: Partial<InsertSite>): Promise<Site>;
  deleteSite(id: number): Promise<void>;

  // Lot operations
  getLotsBySite(siteId: number): Promise<Lot[]>;
  getLot(id: number): Promise<Lot | undefined>;
  createLot(lot: InsertLot): Promise<Lot>;
  updateLot(id: number, lot: Partial<InsertLot>): Promise<Lot>;
  reserveLot(lotId: number, clientId: number, isTemporary?: boolean): Promise<Lot>;
  releaseLot(lotId: number): Promise<Lot>;
  getSiteStats(siteId: number): Promise<{
    total: number;
    available: number;
    reserved: number;
    sold: number;
  }>;

  // Payment operations
  getPayments(filters?: {
    clientId?: number;
    siteId?: number;
    type?: string;
    limit?: number;
    offset?: number;
  }): Promise<Payment[]>;
  getPayment(id: number): Promise<Payment | undefined>;
  createPayment(payment: InsertPayment): Promise<Payment>;
  updatePayment(id: number, payment: Partial<InsertPayment>): Promise<Payment>;

  // Contract operations
  getContracts(filters?: {
    clientId?: number;
    status?: string;
    limit?: number;
    offset?: number;
  }): Promise<Contract[]>;
  getContract(id: number): Promise<Contract | undefined>;
  createContract(contract: InsertContract): Promise<Contract>;
  updateContract(id: number, contract: Partial<InsertContract>): Promise<Contract>;
  generatePaymentSchedule(contractId: number): Promise<PaymentSchedule[]>;

  // Dashboard stats
  getDashboardStats(): Promise<{
    newProspects: number;
    conversions: number;
    availableLots: number;
    totalRevenue: number;
  }>;

  // Activity log
  logActivity(
    userId: string,
    action: string,
    entityType: string,
    entityId: number | null,
    description: string,
    metadata?: any
  ): Promise<void>;
  getRecentActivities(limit?: number): Promise<ActivityLog[]>;
}

export class DatabaseStorage implements IStorage {
  // User operations
  async getUser(id: string): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user;
  }

  async upsertUser(userData: UpsertUser): Promise<User> {
    const [user] = await db
      .insert(users)
      .values(userData)
      .onConflictDoUpdate({
        target: users.id,
        set: {
          ...userData,
          updatedAt: new Date(),
        },
      })
      .returning();
    return user;
  }

  // Prospect operations
  async getProspects(filters?: {
    status?: string;
    assignedToId?: string;
    siteId?: number;
    limit?: number;
    offset?: number;
  }): Promise<Prospect[]> {
    let query = db.select().from(prospects);
    
    const conditions = [];
    if (filters?.status) {
      conditions.push(eq(prospects.status, filters.status as any));
    }
    if (filters?.assignedToId) {
      conditions.push(eq(prospects.assignedToId, filters.assignedToId));
    }
    if (filters?.siteId) {
      conditions.push(eq(prospects.interestedSiteId, filters.siteId));
    }

    if (conditions.length > 0) {
      query = query.where(and(...conditions));
    }

    query = query.orderBy(desc(prospects.createdAt));

    if (filters?.limit) {
      query = query.limit(filters.limit);
    }
    if (filters?.offset) {
      query = query.offset(filters.offset);
    }

    return await query;
  }

  async getProspect(id: number): Promise<Prospect | undefined> {
    const [prospect] = await db.select().from(prospects).where(eq(prospects.id, id));
    return prospect;
  }

  async createProspect(prospect: InsertProspect): Promise<Prospect> {
    const [newProspect] = await db.insert(prospects).values(prospect).returning();
    return newProspect;
  }

  async updateProspect(id: number, prospect: Partial<InsertProspect>): Promise<Prospect> {
    const [updatedProspect] = await db
      .update(prospects)
      .set({ ...prospect, updatedAt: new Date() })
      .where(eq(prospects.id, id))
      .returning();
    return updatedProspect;
  }

  async deleteProspect(id: number): Promise<void> {
    await db.delete(prospects).where(eq(prospects.id, id));
  }

  async assignProspectToCommercial(prospectId: number, commercialId: string): Promise<void> {
    await db
      .update(prospects)
      .set({ assignedToId: commercialId, updatedAt: new Date() })
      .where(eq(prospects.id, prospectId));
  }

  // Site operations
  async getSites(filters?: { isActive?: boolean }): Promise<Site[]> {
    let query = db.select().from(sites);
    
    if (filters?.isActive !== undefined) {
      query = query.where(eq(sites.isActive, filters.isActive));
    }

    return await query.orderBy(desc(sites.createdAt));
  }

  async getSite(id: number): Promise<Site | undefined> {
    const [site] = await db.select().from(sites).where(eq(sites.id, id));
    return site;
  }

  async createSite(site: InsertSite): Promise<Site> {
    const [newSite] = await db.insert(sites).values(site).returning();
    return newSite;
  }

  async updateSite(id: number, site: Partial<InsertSite>): Promise<Site> {
    const [updatedSite] = await db
      .update(sites)
      .set({ ...site, updatedAt: new Date() })
      .where(eq(sites.id, id))
      .returning();
    return updatedSite;
  }

  async deleteSite(id: number): Promise<void> {
    await db.delete(sites).where(eq(sites.id, id));
  }

  // Lot operations
  async getLotsBySite(siteId: number): Promise<Lot[]> {
    return await db
      .select()
      .from(lots)
      .where(eq(lots.siteId, siteId))
      .orderBy(asc(lots.lotNumber));
  }

  async getLot(id: number): Promise<Lot | undefined> {
    const [lot] = await db.select().from(lots).where(eq(lots.id, id));
    return lot;
  }

  async createLot(lot: InsertLot): Promise<Lot> {
    const [newLot] = await db.insert(lots).values(lot).returning();
    return newLot;
  }

  async updateLot(id: number, lot: Partial<InsertLot>): Promise<Lot> {
    const [updatedLot] = await db
      .update(lots)
      .set({ ...lot, updatedAt: new Date() })
      .where(eq(lots.id, id))
      .returning();
    return updatedLot;
  }

  async reserveLot(lotId: number, clientId: number, isTemporary = false): Promise<Lot> {
    const status = isTemporary ? "reserve_temporaire" : "reserve";
    const reservedUntil = isTemporary ? new Date(Date.now() + 48 * 60 * 60 * 1000) : null; // 48h for temporary

    const [reservedLot] = await db
      .update(lots)
      .set({
        status: status as any,
        clientId,
        reservedUntil,
        updatedAt: new Date(),
      })
      .where(eq(lots.id, lotId))
      .returning();
    return reservedLot;
  }

  async releaseLot(lotId: number): Promise<Lot> {
    const [releasedLot] = await db
      .update(lots)
      .set({
        status: "disponible",
        clientId: null,
        reservedUntil: null,
        updatedAt: new Date(),
      })
      .where(eq(lots.id, lotId))
      .returning();
    return releasedLot;
  }

  async getSiteStats(siteId: number): Promise<{
    total: number;
    available: number;
    reserved: number;
    sold: number;
  }> {
    const [stats] = await db
      .select({
        total: count(),
        available: count(sql`CASE WHEN ${lots.status} = 'disponible' THEN 1 END`),
        reserved: count(sql`CASE WHEN ${lots.status} IN ('reserve_temporaire', 'reserve') THEN 1 END`),
        sold: count(sql`CASE WHEN ${lots.status} = 'vendu' THEN 1 END`),
      })
      .from(lots)
      .where(eq(lots.siteId, siteId));

    return {
      total: stats.total,
      available: stats.available,
      reserved: stats.reserved,
      sold: stats.sold,
    };
  }

  // Payment operations
  async getPayments(filters?: {
    clientId?: number;
    siteId?: number;
    type?: string;
    limit?: number;
    offset?: number;
  }): Promise<Payment[]> {
    let query = db.select().from(payments);
    
    const conditions = [];
    if (filters?.clientId) {
      conditions.push(eq(payments.clientId, filters.clientId));
    }
    if (filters?.siteId) {
      conditions.push(eq(payments.siteId, filters.siteId));
    }
    if (filters?.type) {
      conditions.push(eq(payments.type, filters.type as any));
    }

    if (conditions.length > 0) {
      query = query.where(and(...conditions));
    }

    query = query.orderBy(desc(payments.paymentDate));

    if (filters?.limit) {
      query = query.limit(filters.limit);
    }
    if (filters?.offset) {
      query = query.offset(filters.offset);
    }

    return await query;
  }

  async getPayment(id: number): Promise<Payment | undefined> {
    const [payment] = await db.select().from(payments).where(eq(payments.id, id));
    return payment;
  }

  async createPayment(payment: InsertPayment): Promise<Payment> {
    const [newPayment] = await db.insert(payments).values(payment).returning();
    return newPayment;
  }

  async updatePayment(id: number, payment: Partial<InsertPayment>): Promise<Payment> {
    const [updatedPayment] = await db
      .update(payments)
      .set(payment)
      .where(eq(payments.id, id))
      .returning();
    return updatedPayment;
  }

  // Contract operations
  async getContracts(filters?: {
    clientId?: number;
    status?: string;
    limit?: number;
    offset?: number;
  }): Promise<Contract[]> {
    let query = db.select().from(contracts);
    
    const conditions = [];
    if (filters?.clientId) {
      conditions.push(eq(contracts.clientId, filters.clientId));
    }
    if (filters?.status) {
      conditions.push(eq(contracts.status, filters.status as any));
    }

    if (conditions.length > 0) {
      query = query.where(and(...conditions));
    }

    query = query.orderBy(desc(contracts.createdAt));

    if (filters?.limit) {
      query = query.limit(filters.limit);
    }
    if (filters?.offset) {
      query = query.offset(filters.offset);
    }

    return await query;
  }

  async getContract(id: number): Promise<Contract | undefined> {
    const [contract] = await db.select().from(contracts).where(eq(contracts.id, id));
    return contract;
  }

  async createContract(contract: InsertContract): Promise<Contract> {
    const [newContract] = await db.insert(contracts).values(contract).returning();
    return newContract;
  }

  async updateContract(id: number, contract: Partial<InsertContract>): Promise<Contract> {
    const [updatedContract] = await db
      .update(contracts)
      .set({ ...contract, updatedAt: new Date() })
      .where(eq(contracts.id, id))
      .returning();
    return updatedContract;
  }

  async generatePaymentSchedule(contractId: number): Promise<PaymentSchedule[]> {
    const contract = await this.getContract(contractId);
    if (!contract) throw new Error("Contract not found");

    const schedules = [];
    const startDate = new Date();
    
    for (let i = 1; i <= contract.paymentDuration; i++) {
      const dueDate = new Date(startDate);
      dueDate.setMonth(dueDate.getMonth() + i);
      
      schedules.push({
        contractId,
        installmentNumber: i,
        dueDate,
        amount: contract.monthlyAmount,
        isPaid: false,
        paidDate: null,
        paymentId: null,
      });
    }

    const insertedSchedules = await db.insert(paymentSchedule).values(schedules).returning();
    return insertedSchedules;
  }

  // Dashboard stats
  async getDashboardStats(): Promise<{
    newProspects: number;
    conversions: number;
    availableLots: number;
    totalRevenue: number;
  }> {
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

    const [prospectStats] = await db
      .select({
        newProspects: count(sql`CASE WHEN ${prospects.createdAt} >= ${thirtyDaysAgo} THEN 1 END`),
        conversions: count(sql`CASE WHEN ${prospects.status} = 'converti' AND ${prospects.updatedAt} >= ${thirtyDaysAgo} THEN 1 END`),
      })
      .from(prospects);

    const [lotStats] = await db
      .select({
        availableLots: count(sql`CASE WHEN ${lots.status} = 'disponible' THEN 1 END`),
      })
      .from(lots);

    const [revenueStats] = await db
      .select({
        totalRevenue: sql<number>`COALESCE(SUM(${payments.amount}), 0)`,
      })
      .from(payments)
      .where(sql`${payments.paymentDate} >= ${thirtyDaysAgo}`);

    return {
      newProspects: prospectStats.newProspects,
      conversions: prospectStats.conversions,
      availableLots: lotStats.availableLots,
      totalRevenue: revenueStats.totalRevenue,
    };
  }

  // Activity log
  async logActivity(
    userId: string,
    action: string,
    entityType: string,
    entityId: number | null,
    description: string,
    metadata?: any
  ): Promise<void> {
    await db.insert(activityLog).values({
      userId,
      action,
      entityType,
      entityId,
      description,
      metadata,
    });
  }

  async getRecentActivities(limit = 10): Promise<ActivityLog[]> {
    return await db
      .select()
      .from(activityLog)
      .orderBy(desc(activityLog.createdAt))
      .limit(limit);
  }
}

export const storage = new DatabaseStorage();
