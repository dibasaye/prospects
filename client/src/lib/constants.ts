export const PROSPECT_STATUS = {
  NOUVEAU: 'nouveau',
  EN_RELANCE: 'en_relance',
  INTERESSE: 'interesse',
  CONVERTI: 'converti',
  ABANDONNE: 'abandonne',
} as const;

export const PROSPECT_STATUS_LABELS = {
  [PROSPECT_STATUS.NOUVEAU]: 'Nouveau',
  [PROSPECT_STATUS.EN_RELANCE]: 'En relance',
  [PROSPECT_STATUS.INTERESSE]: 'Intéressé',
  [PROSPECT_STATUS.CONVERTI]: 'Converti',
  [PROSPECT_STATUS.ABANDONNE]: 'Abandonné',
} as const;

export const LOT_STATUS = {
  DISPONIBLE: 'disponible',
  RESERVE_TEMPORAIRE: 'reserve_temporaire',
  RESERVE: 'reserve',
  VENDU: 'vendu',
} as const;

export const LOT_STATUS_LABELS = {
  [LOT_STATUS.DISPONIBLE]: 'Disponible',
  [LOT_STATUS.RESERVE_TEMPORAIRE]: 'Réservé temporairement',
  [LOT_STATUS.RESERVE]: 'Réservé',
  [LOT_STATUS.VENDU]: 'Vendu',
} as const;

export const LOT_POSITION = {
  ANGLE: 'angle',
  FACADE: 'facade',
  INTERIEUR: 'interieur',
} as const;

export const LOT_POSITION_LABELS = {
  [LOT_POSITION.ANGLE]: 'Angle',
  [LOT_POSITION.FACADE]: 'Façade',
  [LOT_POSITION.INTERIEUR]: 'Intérieur',
} as const;

export const PAYMENT_TYPE = {
  ADHESION: 'adhesion',
  RESERVATION: 'reservation',
  MENSUALITE: 'mensualite',
} as const;

export const PAYMENT_TYPE_LABELS = {
  [PAYMENT_TYPE.ADHESION]: 'Adhésion',
  [PAYMENT_TYPE.RESERVATION]: 'Réservation',
  [PAYMENT_TYPE.MENSUALITE]: 'Mensualité',
} as const;

export const USER_ROLE = {
  ADMINISTRATEUR: 'administrateur',
  RESPONSABLE_COMMERCIAL: 'responsable_commercial',
  COMMERCIAL: 'commercial',
} as const;

export const USER_ROLE_LABELS = {
  [USER_ROLE.ADMINISTRATEUR]: 'Administrateur',
  [USER_ROLE.RESPONSABLE_COMMERCIAL]: 'Responsable Commercial',
  [USER_ROLE.COMMERCIAL]: 'Commercial',
} as const;

export const CURRENCY = 'FCFA';

export const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount) + ' ' + CURRENCY;
};

export const formatDate = (date: string | Date): string => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(new Date(date));
};

export const formatDateTime = (date: string | Date): string => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(date));
};
