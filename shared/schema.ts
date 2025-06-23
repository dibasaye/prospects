import {
  pgTable,
  text,
  varchar,
  timestamp,
  jsonb,
  index,
  serial,
  integer,
  decimal,
  boolean,
  pgEnum,
} from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";
import { relations } from "drizzle-orm";

// Session storage table for Replit Auth
export const sessions = pgTable(
  "sessions",
  {
    sid: varchar("sid").primaryKey(),
    sess: jsonb("sess").notNull(),
    expire: timestamp("expire").notNull(),
  },
  (table) => [index("IDX_session_expire").on(table.expire)],
);

// User roles enum
export const userRoleEnum = pgEnum("user_role", ["administrateur", "responsable_commercial", "commercial"]);

// User storage table for Replit Auth
export const users = pgTable("users", {
  id: varchar("id").primaryKey().notNull(),
  email: varchar("email").unique(),
  firstName: varchar("first_name"),
  lastName: varchar("last_name"),
  profileImageUrl: varchar("profile_image_url"),
  role: userRoleEnum("role").default("commercial").notNull(),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

// Prospect status enum
export const prospectStatusEnum = pgEnum("prospect_status", ["nouveau", "en_relance", "interesse", "converti", "abandonne"]);

// Prospects table
export const prospects = pgTable("prospects", {
  id: serial("id").primaryKey(),
  firstName: varchar("first_name").notNull(),
  lastName: varchar("last_name").notNull(),
  phone: varchar("phone").notNull(),
  phoneSecondary: varchar("phone_secondary"),
  email: varchar("email"),
  address: text("address"),
  idDocument: varchar("id_document"), // path to uploaded document
  representativeName: varchar("representative_name"),
  representativePhone: varchar("representative_phone"),
  representativeAddress: text("representative_address"),
  status: prospectStatusEnum("status").default("nouveau").notNull(),
  interestedSiteId: integer("interested_site_id"),
  assignedToId: varchar("assigned_to_id"),
  createdById: varchar("created_by_id").notNull(),
  notes: text("notes"),
  lastContactDate: timestamp("last_contact_date"),
  nextFollowUpDate: timestamp("next_follow_up_date"),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

// Sites table
export const sites = pgTable("sites", {
  id: serial("id").primaryKey(),
  name: varchar("name").notNull(),
  location: varchar("location").notNull(),
  department: varchar("department"),
  commune: varchar("commune"),
  gpsCoordinates: varchar("gps_coordinates"),
  adhesionFee: integer("adhesion_fee").notNull(), // in FCFA
  reservationFee: integer("reservation_fee").notNull(), // in FCFA
  totalLots: integer("total_lots").notNull(),
  planImage: varchar("plan_image"), // path to plan image
  launchDate: timestamp("launch_date"),
  isActive: boolean("is_active").default(true),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

// Lot status enum
export const lotStatusEnum = pgEnum("lot_status", ["disponible", "reserve_temporaire", "reserve", "vendu"]);

// Lot position enum
export const lotPositionEnum = pgEnum("lot_position", ["angle", "facade", "interieur"]);

// Lots table
export const lots = pgTable("lots", {
  id: serial("id").primaryKey(),
  siteId: integer("site_id").notNull(),
  lotNumber: varchar("lot_number").notNull(),
  surface: decimal("surface", { precision: 10, scale: 2 }),
  position: lotPositionEnum("position").default("interieur").notNull(),
  basePrice: integer("base_price").notNull(), // in FCFA
  finalPrice: integer("final_price").notNull(), // with position adjustments
  status: lotStatusEnum("status").default("disponible").notNull(),
  clientId: integer("client_id"), // prospect id when reserved/sold
  reservedUntil: timestamp("reserved_until"), // for temporary reservations
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

// Payment type enum
export const paymentTypeEnum = pgEnum("payment_type", ["adhesion", "reservation", "mensualite"]);

// Payments table
export const payments = pgTable("payments", {
  id: serial("id").primaryKey(),
  clientId: integer("client_id").notNull(),
  siteId: integer("site_id").notNull(),
  lotId: integer("lot_id"),
  type: paymentTypeEnum("type").notNull(),
  amount: integer("amount").notNull(), // in FCFA
  paymentMethod: varchar("payment_method"), // cash, bank_transfer, etc.
  paymentDate: timestamp("payment_date").defaultNow(),
  receiptNumber: varchar("receipt_number"),
  notes: text("notes"),
  createdById: varchar("created_by_id").notNull(),
  createdAt: timestamp("created_at").defaultNow(),
});

// Contract status enum
export const contractStatusEnum = pgEnum("contract_status", ["brouillon", "genere", "signe", "archive"]);

// Contracts table
export const contracts = pgTable("contracts", {
  id: serial("id").primaryKey(),
  clientId: integer("client_id").notNull(),
  siteId: integer("site_id").notNull(),
  lotId: integer("lot_id").notNull(),
  contractNumber: varchar("contract_number").notNull().unique(),
  totalAmount: integer("total_amount").notNull(), // in FCFA
  paidAmount: integer("paid_amount").default(0).notNull(),
  paymentDuration: integer("payment_duration").notNull(), // in months (12, 24, 36)
  monthlyAmount: integer("monthly_amount").notNull(),
  status: contractStatusEnum("status").default("brouillon").notNull(),
  contractPath: varchar("contract_path"), // path to generated PDF
  signedContractPath: varchar("signed_contract_path"), // path to signed PDF
  generatedAt: timestamp("generated_at"),
  signedAt: timestamp("signed_at"),
  createdById: varchar("created_by_id").notNull(),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

// Payment schedule table
export const paymentSchedule = pgTable("payment_schedule", {
  id: serial("id").primaryKey(),
  contractId: integer("contract_id").notNull(),
  installmentNumber: integer("installment_number").notNull(),
  dueDate: timestamp("due_date").notNull(),
  amount: integer("amount").notNull(),
  isPaid: boolean("is_paid").default(false),
  paidDate: timestamp("paid_date"),
  paymentId: integer("payment_id"), // link to payments table when paid
  createdAt: timestamp("created_at").defaultNow(),
});

// Activity log table
export const activityLog = pgTable("activity_log", {
  id: serial("id").primaryKey(),
  userId: varchar("user_id").notNull(),
  action: varchar("action").notNull(),
  entityType: varchar("entity_type").notNull(), // prospect, site, lot, payment, contract
  entityId: integer("entity_id"),
  description: text("description").notNull(),
  metadata: jsonb("metadata"), // additional data
  createdAt: timestamp("created_at").defaultNow(),
});

// Relations
export const usersRelations = relations(users, ({ many }) => ({
  assignedProspects: many(prospects),
  createdProspects: many(prospects),
  payments: many(payments),
  contracts: many(contracts),
  activities: many(activityLog),
}));

export const prospectsRelations = relations(prospects, ({ one, many }) => ({
  interestedSite: one(sites, {
    fields: [prospects.interestedSiteId],
    references: [sites.id],
  }),
  assignedTo: one(users, {
    fields: [prospects.assignedToId],
    references: [users.id],
  }),
  createdBy: one(users, {
    fields: [prospects.createdById],
    references: [users.id],
  }),
  payments: many(payments),
  contracts: many(contracts),
  reservedLots: many(lots),
}));

export const sitesRelations = relations(sites, ({ many }) => ({
  lots: many(lots),
  interestedProspects: many(prospects),
  payments: many(payments),
  contracts: many(contracts),
}));

export const lotsRelations = relations(lots, ({ one, many }) => ({
  site: one(sites, {
    fields: [lots.siteId],
    references: [sites.id],
  }),
  client: one(prospects, {
    fields: [lots.clientId],
    references: [prospects.id],
  }),
  payments: many(payments),
  contracts: many(contracts),
}));

export const paymentsRelations = relations(payments, ({ one }) => ({
  client: one(prospects, {
    fields: [payments.clientId],
    references: [prospects.id],
  }),
  site: one(sites, {
    fields: [payments.siteId],
    references: [sites.id],
  }),
  lot: one(lots, {
    fields: [payments.lotId],
    references: [lots.id],
  }),
  createdBy: one(users, {
    fields: [payments.createdById],
    references: [users.id],
  }),
}));

export const contractsRelations = relations(contracts, ({ one, many }) => ({
  client: one(prospects, {
    fields: [contracts.clientId],
    references: [prospects.id],
  }),
  site: one(sites, {
    fields: [contracts.siteId],
    references: [sites.id],
  }),
  lot: one(lots, {
    fields: [contracts.lotId],
    references: [lots.id],
  }),
  createdBy: one(users, {
    fields: [contracts.createdById],
    references: [users.id],
  }),
  paymentSchedule: many(paymentSchedule),
}));

export const paymentScheduleRelations = relations(paymentSchedule, ({ one }) => ({
  contract: one(contracts, {
    fields: [paymentSchedule.contractId],
    references: [contracts.id],
  }),
  payment: one(payments, {
    fields: [paymentSchedule.paymentId],
    references: [payments.id],
  }),
}));

export const activityLogRelations = relations(activityLog, ({ one }) => ({
  user: one(users, {
    fields: [activityLog.userId],
    references: [users.id],
  }),
}));

// Insert schemas
export const insertProspectSchema = createInsertSchema(prospects).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertSiteSchema = createInsertSchema(sites).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertLotSchema = createInsertSchema(lots).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertPaymentSchema = createInsertSchema(payments).omit({
  id: true,
  createdAt: true,
});

export const insertContractSchema = createInsertSchema(contracts).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

// Types
export type UpsertUser = typeof users.$inferInsert;
export type User = typeof users.$inferSelect;
export type InsertProspect = z.infer<typeof insertProspectSchema>;
export type Prospect = typeof prospects.$inferSelect;
export type InsertSite = z.infer<typeof insertSiteSchema>;
export type Site = typeof sites.$inferSelect;
export type InsertLot = z.infer<typeof insertLotSchema>;
export type Lot = typeof lots.$inferSelect;
export type InsertPayment = z.infer<typeof insertPaymentSchema>;
export type Payment = typeof payments.$inferSelect;
export type InsertContract = z.infer<typeof insertContractSchema>;
export type Contract = typeof contracts.$inferSelect;
export type PaymentSchedule = typeof paymentSchedule.$inferSelect;
export type ActivityLog = typeof activityLog.$inferSelect;
