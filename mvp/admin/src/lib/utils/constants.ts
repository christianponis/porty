export const statusLabels: Record<string, string> = {
  pending: "In attesa",
  confirmed: "Confermata",
  completed: "Completata",
  cancelled: "Cancellata",
};

export const statusColors: Record<string, "warning" | "info" | "success" | "danger" | "default"> = {
  pending: "warning",
  confirmed: "info",
  completed: "success",
  cancelled: "danger",
};

export const roleLabels: Record<string, string> = {
  admin: "Admin",
  owner: "Proprietario",
  guest: "Ospite",
};

export const roleColors: Record<string, "danger" | "primary" | "success" | "default"> = {
  admin: "danger",
  owner: "primary",
  guest: "success",
};

export const ratingLevelLabels: Record<string, string> = {
  grey: "Grigia",
  blue: "Blu",
  gold: "Oro",
};

export const ratingLevelColors: Record<string, "default" | "info" | "warning"> = {
  grey: "default",
  blue: "info",
  gold: "warning",
};

export const conventionCategoryLabels: Record<string, string> = {
  commercial: "Commerciale",
  technical: "Tecnico",
  tourism: "Turismo",
  health: "Salute",
  transport: "Trasporto",
  other: "Altro",
};

export const conventionCategoryColors: Record<string, "primary" | "cyan" | "purple" | "danger" | "warning" | "default"> = {
  commercial: "primary",
  technical: "cyan",
  tourism: "purple",
  health: "danger",
  transport: "warning",
  other: "default",
};

export const discountTypeLabels: Record<string, string> = {
  percentage: "Percentuale",
  fixed: "Fisso (EUR)",
  free: "Gratuito",
};
