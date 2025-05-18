import React, { useState } from 'react';
import { Box, Button, Paper, Collapse, TextField } from '@mui/material';
import { Add, ExpandLess, ExpandMore } from '@mui/icons-material';
import { useForm, router } from '@inertiajs/react';

export default function EvaluationCreateBox() {
  const [open, setOpen] = useState(false);
  const form = useForm({ title: '', description: '' });

  const submit = e => {
    e.preventDefault();
    form.post(route('evaluations.store'), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        setOpen(false);
      },
    });
  };

  return (
    <Box mt={2}>
      <Button
        startIcon={<Add />}
        endIcon={open ? <ExpandLess /> : <ExpandMore />}
        onClick={() => setOpen(!open)}
      >
        Create Evaluation
      </Button>

      <Collapse in={open} unmountOnExit>
        <Paper sx={{ p: 3, mt: 1 }}>
          <form onSubmit={submit}>
            <TextField
              label="Evaluation Title"
              fullWidth
              required
              margin="normal"
              value={form.data.title}
              onChange={e => form.setData('title', e.target.value)}
              error={!!form.errors.title}
              helperText={form.errors.title}
            />
            <TextField
              label="Description"
              fullWidth
              multiline
              rows={3}
              margin="normal"
              value={form.data.description}
              onChange={e => form.setData('description', e.target.value)}
              error={!!form.errors.description}
              helperText={form.errors.description}
            />
            <Button type="submit" variant="contained">Save Evaluation</Button>
          </form>
        </Paper>
      </Collapse>
    </Box>
  );
}
