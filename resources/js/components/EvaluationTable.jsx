import React from 'react';
import {
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
  Paper, IconButton,
} from '@mui/material';
import { Edit, Delete } from '@mui/icons-material';
import { router } from '@inertiajs/react';   // ↔ if you don’t use Inertia, swap for your fetch/axios logic

export default function EvaluationTable({ evaluations = [] }) {

  const destroy = id => {
    if (confirm('Delete this evaluation?')) {
      router.delete(route('evaluations.destroy', id), { preserveScroll: true });
    }
  };

  const quickEdit = ev => {
    const title = prompt('New title', ev.title);
    if (title !== null) {
      router.put(route('evaluations.update', ev.id),
        { title, description: ev.description },
        { preserveScroll: true });
    }
  };

  return (
    <TableContainer component={Paper}>
      <Table>
        <TableHead>
          <TableRow>
            <TableCell width="35%">Title</TableCell>
            <TableCell>Description</TableCell>
            <TableCell align="center" width="120">Edit</TableCell>
            <TableCell align="center" width="120">Delete</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {evaluations.map(ev => (
            <TableRow key={ev.id}>
              <TableCell>{ev.title}</TableCell>
              <TableCell>{ev.description}</TableCell>
              <TableCell align="center">
                <IconButton size="small" color="primary" onClick={() => quickEdit(ev)}>
                  <Edit />
                </IconButton>
              </TableCell>
              <TableCell align="center">
                <IconButton size="small" color="error" onClick={() => destroy(ev.id)}>
                  <Delete />
                </IconButton>
              </TableCell>
            </TableRow>
          ))}
          {evaluations.length === 0 && (
            <TableRow>
              <TableCell colSpan={4} align="center">No evaluations.</TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>
    </TableContainer>
  );
}
